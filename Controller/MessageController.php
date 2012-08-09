<?php

/*
 * This file is part of the CCDNMessage MessageBundle
 *
 * (c) CCDN (c) CodeConsortium <http://www.codeconsortium.com/>
 *
 * Available on github <http://www.github.com/codeconsortium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CCDNMessage\MessageBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
class MessageController extends ContainerAware
{

    /**
     *
     * @access public
     * @param  Int $messageId
     * @return RenderResponse
     */
    public function showMessageAction($messageId)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException('You do not have access to this section.');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        //
        // Get all the folders.
        //
        $folders = $this->container->get('ccdn_message_message.folder.repository')->findAllFoldersForUser($user->getId());

        $folderManager = $this->container->get('ccdn_message_message.folder.manager');

        if ( ! $folders) {
            $folderManager->setupDefaults($user->getId())->flush();

            $folders = $this->container->get('ccdn_message_message.folder.repository')->findAllFoldersForUser($user->getId());
        }

        //
        // Get the message.
        //
        $message = $this->container->get('ccdn_message_message.message.repository')->findMessageByIdForUser($messageId, $user->getId());

        if (! $message) {
            throw new NotFoundHttpException('No such message found!');
        }

        $quota = $this->container->getParameter('ccdn_message_message.quotas.max_messages');

        $currentFolder = $folderManager->getCurrentFolder($folders, $message->getFolder()->getName());
        $stats = $folderManager->getUsedAllowance($folders, $quota);
        $totalMessageCount = $stats['total_message_count'];
        $usedAllowance = $stats['used_allowance'];

        $this->container->get('ccdn_message_message.message.manager')->markAsRead($message)->flush()->updateAllFolderCachesForUser($user);

        $crumbs = $this->container->get('ccdn_component_crumb.trail')
            ->add($this->container->get('translator')->trans('crumbs.message_index', array(), 'CCDNMessageMessageBundle'), $this->container->get('router')->generate('ccdn_message_message_index'), "home")
            ->add($message->getFolder()->getName(), $this->container->get('router')->generate('ccdn_message_message_folder_show', array('folderName' => $message->getFolder()->getName())), "folder")
            ->add($message->getSubject(), $this->container->get('router')->generate('ccdn_message_message_mail_show_by_id', array('messageId' => $messageId)), "email");

        return $this->container->get('templating')->renderResponse('CCDNMessageMessageBundle:Message:show.html.' . $this->getEngine(), array(
            'user_profile_route' => $this->container->getParameter('ccdn_message_message.user.profile_route'),
            'user' => $user,
            'crumbs' => $crumbs,
            'folders' => $folders,
            'current_folder' => $currentFolder,
            'used_allowance' => $usedAllowance,
            'message' => $message,
        ));
    }

    /**
     *
     * @access public
     * @param  Int $userId
     * @return RedirectResponse|RenderResponse
     */
    public function composeAction($userId)
    {
        //
        //	Invalidate this action / redirect if user should not have access to it
        //
        if ( ! $this->container->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException('You do not have permission to use this resource!');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        //
        // Are we sending this to someone who's 'send message' button we clicked?
        //
        if ($userId) {
            $sendTo = $this->container->get('ccdn_user_user.user.repository')->findOneById($userId);

            $formHandler = $this->container->get('ccdn_message_message.message.form.handler')->setDefaultValues(array('sender' => $user, 'send_to' => $sendTo));
        } else {
            $formHandler = $this->container->get('ccdn_message_message.message.form.handler')->setDefaultValues(array('sender' => $user));
        }

        if (isset($_POST['submit_draft'])) {
            $formHandler->setMode($formHandler::DRAFT);

            if ($formHandler->process()) {
                return new RedirectResponse($this->container->get('router')->generate('ccdn_message_message_folder_show', array('folder_name' => 'drafts')));
            }
        }

        if (isset($_POST['submit_post'])) {
            if ($formHandler->process()) {
                return new RedirectResponse($this->container->get('router')->generate('ccdn_message_message_folder_show', array('folder_name' => 'sent')));
            }
        }

        if (isset($_POST['submit_preview'])) {
            $formHandler->setMode($formHandler::PREVIEW);
        }

        //
        // Get all the folders.
        //
        $folders = $this->container->get('ccdn_message_message.folder.repository')->findAllFoldersForUser($user->getId());

        $folderManager = $this->container->get('ccdn_message_message.folder.manager');

        if (! $folders) {
            $folderManager->setupDefaults($user->getId())->flush();

            $folders = $this->container->get('ccdn_message_message.folder.repository')->findAllFoldersForUser($user->getId());
        }

        // setup crumb trail.
        $crumbs = $this->container->get('ccdn_component_crumb.trail')
            ->add($this->container->get('translator')->trans('crumbs.message_index', array(), 'CCDNMessageMessageBundle'), $this->container->get('router')->generate('ccdn_message_message_index'), "home")
            ->add($this->container->get('translator')->trans('crumbs.compose_message', array(), 'CCDNMessageMessageBundle'), $this->container->get('router')->generate('ccdn_message_message_mail_compose'), "edit");

        return $this->container->get('templating')->renderResponse('CCDNMessageMessageBundle:Message:compose.html.' . $this->getEngine(), array(
            'user_profile_route' => $this->container->getParameter('ccdn_message_message.user.profile_route'),
            'crumbs' => $crumbs,
            'form' => $formHandler->getForm()->createView(),
            'preview' => $formHandler->getForm()->getData(),
            'folders' => $folders,
            'user' => $user,
        ));
    }



    /**
     *
     * @access public
     * @param  Int $messageId
     * @return RedirectResponse|RenderResponse
     */
    public function replyAction($messageId)
    {
        //
        //	Invalidate this action / redirect if user should not have access to it
        //
        if ( ! $this->container->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException('You do not have permission to use this resource!');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $message = $this->container->get('ccdn_message_message.message.repository')->findMessageByIdForUser($messageId, $user->getId());

        if (! $message) {
            throw new NotFoundHttpException('No such message found!');
        }

        $formHandler = $this->container->get('ccdn_message_message.message.form.handler')->setDefaultValues(array('sender' => $user, 'message' => $message, 'action' => 'reply'));

        if ($formHandler->process()) {
            $this->container->get('session')->setFlash('notice', $this->container->get('translator')->trans('flash.message.sent.success', array(), 'CCDNMessageMessageBundle'));

            return new RedirectResponse($this->container->get('router')->generate('ccdn_message_message_folder_show', array('folder_name' => 'sent')));
        } else {
            //
            // Get all the folders.
            //
            $folders = $this->container->get('ccdn_message_message.folder.repository')->findAllFoldersForUser($user->getId());

            $folderManager = $this->container->get('ccdn_message_message.folder.manager');

            if (! $folders) {
                $folderManager->setupDefaults($user->getId())->flush();

                $folders = $this->container->get('ccdn_message_message.folder.repository')->findAllFoldersForUser($user->getId());
            }

            // setup crumb trail.
            $crumbs = $this->container->get('ccdn_component_crumb.trail')
                ->add($this->container->get('translator')->trans('crumbs.message_index', array(), 'CCDNMessageMessageBundle'), $this->container->get('router')->generate('ccdn_message_message_index'), "home")
                ->add($message->getSubject(), $this->container->get('router')->generate('ccdn_message_message_mail_show_by_id', array('messageId' => $messageId)), "email")
                ->add($this->container->get('translator')->trans('crumbs.compose_reply', array(), 'CCDNMessageMessageBundle'), $this->container->get('router')->generate('ccdn_message_message_mail_compose_reply', array('messageId' => $messageId)), "edit");

            return $this->container->get('templating')->renderResponse('CCDNMessageMessageBundle:Message:compose.html.' . $this->getEngine(), array(
                'user_profile_route' => $this->container->getParameter('ccdn_message_message.user.profile_route'),
                'crumbs' => $crumbs,
                'form' => $formHandler->getForm()->createView(),
                'preview' => $formHandler->getForm()->getData(),
                'folders' => $folders,
                'user' => $user,
            ));
        }
    }



    /**
     *
     * @access public
     * @param  Int $messageId
     * @return RedirectResponse|RenderResponse
     */
    public function forwardAction($messageId)
    {
        //
        //	Invalidate this action / redirect if user should not have access to it
        //
        if ( ! $this->container->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException('You do not have permission to use this resource!');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $message = $this->container->get('ccdn_message_message.message.repository')->findMessageByIdForUser($messageId, $user->getId());

        if (! $message) {
            throw new NotFoundHttpException('No such message found!');
        }

        $formHandler = $this->container->get('ccdn_message_message.message.form.handler')->setDefaultValues(array('sender' => $user, 'message' => $message, 'action' => 'forward'));

        if ($formHandler->process()) {
            $this->container->get('session')->setFlash('notice', $this->container->get('translator')->trans('flash.message.sent.success', array(), 'CCDNMessageMessageBundle'));

            return new RedirectResponse($this->container->get('router')->generate('ccdn_message_message_folder_show', array('folder_name' => 'sent')));
        } else {
            //
            // Get all the folders.
            //
            $folders = $this->container->get('ccdn_message_message.folder.repository')->findAllFoldersForUser($user->getId());

            $folderManager = $this->container->get('ccdn_message_message.folder.manager');

            if (! $folders) {
                $folderManager->setupDefaults($user->getId())->flush();

                $folders = $this->container->get('ccdn_message_message.folder.repository')->findAllFoldersForUser($user->getId());
            }

            // setup crumb trail.
            $crumbs = $this->container->get('ccdn_component_crumb.trail')
                ->add($this->container->get('translator')->trans('crumbs.message_index', array(), 'CCDNMessageMessageBundle'), $this->container->get('router')->generate('ccdn_message_message_index'), "home")
                ->add($message->getSubject(), $this->container->get('router')->generate('ccdn_message_message_mail_show_by_id', array('messageId' => $messageId)), "email")
                ->add($this->container->get('translator')->trans('crumbs.compose_forward', array(), 'CCDNMessageMessageBundle'), $this->container->get('router')->generate('ccdn_message_message_mail_compose_forward', array('messageId' => $messageId)), "edit");

            return $this->container->get('templating')->renderResponse('CCDNMessageMessageBundle:Message:compose.html.' . $this->getEngine(), array(
                'user_profile_route' => $this->container->getParameter('ccdn_message_message.user.profile_route'),
                'crumbs' => $crumbs,
                'form' => $formHandler->getForm()->createView(),
                'preview' => $formHandler->getForm()->getData(),
                'folders' => $folders,
                'user' => $user,
            ));
        }
    }



    /**
    * @access public
    * @param Int $messageId
    * @return RedirectResponse
    */
    public function sendDraftAction($messageId)
    {
        //
        //	Invalidate this action / redirect if user should not have access to it
        //
        if ( ! $this->container->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException('You do not have permission to use this resource!');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $message = $this->container->get('ccdn_message_message.message.repository')->findMessageByIdForUser($messageId, $user->getId());

        if (! $message) {
            throw new NotFoundHttpException('No such message found!');
        }

        $this->container->get('ccdn_message_message.message.manager')->sendDraft(array($message))->flush();

        return new RedirectResponse($this->container->get('router')->generate('ccdn_message_message_folder_show', array('folder_name' => 'sent')));
    }



    /**
     *
     * @access public
     * @param  Int $messageId
     * @return RedirectResponse
     */
    public function markAsReadAction($messageId)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException('You do not have access to this section.');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $message = $this->container->get('ccdn_message_message.message.repository')->findMessageByIdForUser($messageId, $user->getId());

        if (! $message) {
            throw new NotFoundHttpException('No such message found!');
        }

        $this->container->get('ccdn_message_message.message.manager')->markAsRead($message)->flush()->updateAllFolderCachesForUser($user);

        return new RedirectResponse($this->container->get('router')->generate('ccdn_message_message_folder_show', array('folder_name' => $message->getFolder()->getName())));
    }



    /**
     *
     * @access public
     * @param  Int $messageId
     * @return RedirectResponse
     */
    public function markAsUnreadAction($messageId)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException('You do not have access to this section.');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $message = $this->container->get('ccdn_message_message.message.repository')->findMessageByIdForUser($messageId, $user->getId());

        if (! $message) {
            throw new NotFoundHttpException('No such message found!');
        }

        $this->container->get('ccdn_message_message.message.manager')->markAsUnread($message)->flush()->updateAllFolderCachesForUser($user);

        return new RedirectResponse($this->container->get('router')->generate('ccdn_message_message_folder_show', array('folder_name' => $message->getFolder()->getName())));
    }



    /**
     *
     * @access public
     * @param  Int $messageId
     * @return RedirectResponse
     */
    public function deleteAction($messageId)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException('You do not have access to this section.');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $message = $this->container->get('ccdn_message_message.message.repository')->findMessageByIdForUser($messageId, $user->getId());
        $folders = $this->container->get('ccdn_message_message.folder.repository')->findAllFoldersForUser($user->getId());

        if ( ! $folders) {
            $this->container->get('ccdn_message_message.folder.manager')->setupDefaults($user->getId())->flush();

            $folders = $this->container->get('ccdn_message_message.folder.repository')->findAllFoldersForUser($user->getId());
        }

        if ( ! $message) {
            throw new NotFoundHttpException('No such message found!');
        }

        $this->container->get('ccdn_message_message.message.manager')->delete($message, $folders)->flush()->updateAllFolderCachesForUser($user);

        return new RedirectResponse($this->container->get('router')->generate('ccdn_message_message_folder_show', array('folder_name' => $message->getFolder()->getName())));
    }



    /**
     *
     * @access protected
     * @return String
     */
    protected function getEngine()
    {
        return $this->container->getParameter('ccdn_message_message.template.engine');
    }

}
