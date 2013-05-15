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

use CCDNMessage\MessageBundle\Controller\MessageBaseController;

/**
 *
 * @category CCDNMessage
 * @package  MessageBundle
 *
 * @author   Reece Fowell <reece@codeconsortium.com>
 * @license  http://opensource.org/licenses/MIT MIT
 * @version  Release: 2.0
 * @link     https://github.com/codeconsortium/CCDNMessageMessageBundle
 *
 */
class MessageController extends MessageBaseController
{
    /**
     *
     * @access public
     * @param  int            $envelopeId
     * @return RenderResponse
     */
    public function showMessageAction($envelopeId)
    {
        $this->isAuthorised('ROLE_USER');

        $user = $this->getUser();

        // Get the message.
        $envelope = $this->getEnvelopeManager()->findEnvelopeByIdForUser($envelopeId, $user->getId());
        $this->isFound($envelope);

        // Get message thread.
        $thread = $this->getThreadManager()->findThreadWithAllEnvelopesByThreadIDAndUserId($envelope->getThread()->getId(), $user->getId());

        $folders = $this->getFolderManager()->findAllFoldersForUserById($user->getId());
        $currentFolder = $this->getFolderManager()->getCurrentFolder($folders, $envelope->getFolder()->getName());

        $this->getEnvelopeManager()->markAsRead($envelope, $folders)->flush();
        $message = $envelope->getMessage();

        $crumbs = $this->getCrumbs()
            ->add($envelope->getFolder()->getName(), $this->path('ccdn_message_message_folder_show', array('folderName' => $envelope->getFolder()->getName())))
            ->add($message->getSubject(), $this->path('ccdn_message_message_mail_show_by_id', array('envelopeId' => $envelopeId)));

        return $this->renderResponse('CCDNMessageMessageBundle:Message:show.html.',
            array(
                'crumbs' => $crumbs,
                'folders' => $folders,
                'envelope' => $envelope,
                'thread' => $thread,
            )
        );
    }

    /**
     *
     * @access public
     * @param  int                             $userId
     * @return RedirectResponse|RenderResponse
     */
    public function composeAction($userId)
    {
        $this->isAuthorised('ROLE_USER');

        $formHandler = $this->getFormHandlerToSendMessage($userId);

        // Flood Control.
        if (! $this->getFloodControl()->isFlooded()) {
            if ($formHandler->process($this->getRequest())) {
                $this->getFloodControl()->incrementCounter();

                return $this->redirectResponse($this->path('ccdn_message_message_folder_show', array('folderName' => 'sent')));
            }
        } else {
            $this->setFlash('warning', $this->trans('flash.error.message.flood_control'));
        }

        // setup crumb trail.
        $crumbs = $this->getCrumbs()
            ->add($this->trans('crumbs.folder.index'), $this->path('ccdn_message_message_index'))
            ->add($this->trans('crumbs.message.compose.new'), $this->path('ccdn_message_message_mail_compose'));

        return $this->renderResponse('CCDNMessageMessageBundle:Message:compose.html.',
            array(
                'crumbs' => $crumbs,
                'form' => $formHandler->getForm()->createView(),
                'preview' => $formHandler->getForm()->getData(),
            )
        );
    }

    /**
     *
     * @access public
     * @param  int                             $envelopeId
     * @return RedirectResponse|RenderResponse
     */
    public function replyAction($envelopeId)
    {
        $this->isAuthorised('ROLE_USER');

        $envelope = $this->getEnvelopeManager()->findEnvelopeByIdForUser($envelopeId, $this->getUser()->getId());
        $this->isFound($envelope, 'Message could not be found.');

        $formHandler = $this->getFormHandlerToReplyToMessage($envelope);

        // Flood Control.
        if (! $this->getFloodControl()->isFlooded()) {
            if ($formHandler->process($this->getRequest())) {
                 $this->getFloodControl()->incrementCounter();

                $this->setFlash('notice', $this->trans('flash.success.message.sent'));

                return $this->redirectResponse($this->path('ccdn_message_message_folder_show', array('folderName' => 'sent')));
            }
        } else {
            $this->setFlash('warning', $this->trans('flash.error.message.flood_control'));
        }

        // setup crumb trail.
        $crumbs = $this->getCrumbs()
            ->add($this->trans('crumbs.folder.index'), $this->path('ccdn_message_message_index'))
            ->add($envelope->getMessage()->getSubject(), $this->path('ccdn_message_message_mail_show_by_id', array('envelopeId' => $envelopeId)))
            ->add($this->trans('crumbs.message.compose.reply'), $this->path('ccdn_message_message_mail_compose_reply', array('envelopeId' => $envelopeId)));

        return $this->renderResponse('CCDNMessageMessageBundle:Message:composeReply.html.',
            array(
                'crumbs' => $crumbs,
                'form' => $formHandler->getForm()->createView(),
                'preview' => $formHandler->getForm()->getData(),
                'envelope' => $envelope,
            )
        );
    }

    /**
     *
     * @access public
     * @param  int                             $envelopeId
     * @return RedirectResponse|RenderResponse
     */
    public function forwardAction($envelopeId)
    {
        $this->isAuthorised('ROLE_USER');

        $envelope = $this->getEnvelopeManager()->findEnvelopeByIdForUser($envelopeId, $this->getUser()->getId());
        $this->isFound($envelope, 'Message could not be found.');

        $formHandler = $this->getFormHandlerToForwardMessage($envelope);

        // Flood Control.
        if (! $this->getFloodControl()->isFlooded()) {
            if ($formHandler->process($this->getRequest())) {
                $this->getFloodControl()->incrementCounter();

                $this->setFlash('notice', $this->trans('flash.success.message.sent'));

                return $this->redirectResponse($this->path('ccdn_message_message_folder_show', array('folderName' => 'sent')));
            }
        } else {
            $this->setFlash('warning', $this->trans('flash.error.message.flood_control'));
        }

        // setup crumb trail.
        $crumbs = $this->getCrumbs()
            ->add($this->trans('crumbs.folder.index'), $this->path('ccdn_message_message_index'))
            ->add($envelope->getMessage()->getSubject(), $this->path('ccdn_message_message_mail_show_by_id', array('envelopeId' => $envelopeId)))
            ->add($this->trans('crumbs.message.compose.forward'), $this->path('ccdn_message_message_mail_compose_forward', array('envelopeId' => $envelopeId)));

        return $this->renderResponse('CCDNMessageMessageBundle:Message:composeForward.html.',
            array(
                'crumbs' => $crumbs,
                'form' => $formHandler->getForm()->createView(),
                'preview' => $formHandler->getForm()->getData(),
                'envelope' => $envelope,
            )
        );
    }

    /**
     * 
     * @access public
     * @param int $messageId
     * @return RedirectResponse
     */
    public function sendDraftAction($messageId)
    {
        $this->isAuthorised('ROLE_USER');

        $user = $this->getUser();

        $message = $this->getMessageManager()->findMessageByIdForUser($messageId, $user->getId());
        $this->isFound($message);

        // Flood Control.
        if (! $this->getFloodControl()->isFlooded()) {
            $this->getFloodControl()->incrementCounter();

            $this->getMessageManager()->sendDraft(array($message))->flush();
        } else {
            $this->setFlash('warning', $this->trans('flash.error.message.flood_control'));
        }

        return $this->redirectResponse($this->path('ccdn_message_message_folder_show',
            array(
                'folderName' => 'sent'
            )
        ));
    }

    /**
     *
     * @access public
     * @param  int              $envelopeId
     * @return RedirectResponse
     */
    public function markAsReadAction($envelopeId)
    {
        $this->isAuthorised('ROLE_USER');

        $user = $this->getUser();

        $envelope = $this->getEnvelopeManager()->findEnvelopeByIdForUser($envelopeId, $user->getId());
        $this->isFound($envelope);

        $folders = $this->getFolderManager()->findAllFoldersForUserById($user->getId());
        $currentFolder = $this->getFolderManager()->getCurrentFolder($folders, $envelope->getFolder()->getName());

        $this->getEnvelopeManager()->markAsRead($envelope, $folders)->flush();

        return $this->redirectResponse($this->path('ccdn_message_message_folder_show',
            array(
                'folderName' => $currentFolder->getName()
            )
        ));
    }

    /**
     *
     * @access public
     * @param  int              $envelopeId
     * @return RedirectResponse
     */
    public function markAsUnreadAction($envelopeId)
    {
        $this->isAuthorised('ROLE_USER');

        $user = $this->getUser();

        $envelope = $this->getEnvelopeManager()->findEnvelopeByIdForUser($envelopeId, $user->getId());
        $this->isFound($envelope);

        $folders = $this->getFolderManager()->findAllFoldersForUserById($user->getId());
        $currentFolder = $this->getFolderManager()->getCurrentFolder($folders, $envelope->getFolder()->getName());

        $this->getEnvelopeManager()->markAsUnread($envelope, $folders)->flush();

        return $this->redirectResponse($this->path('ccdn_message_message_folder_show',
            array(
                'folderName' => $currentFolder->getName()
            )
        ));
    }

    /**
     *
     * @access public
     * @param  int              $envelopeId
     * @return RedirectResponse
     */
    public function deleteAction($envelopeId)
    {
        $this->isAuthorised('ROLE_USER');

        $user = $this->getUser();

        $envelope = $this->getEnvelopeManager()->findEnvelopeByIdForUser($envelopeId, $user->getId());
        $this->isFound($envelope);

        $folders = $this->getFolderManager()->findAllFoldersForUserById($user->getId());
        $currentFolder = $this->getFolderManager()->getCurrentFolder($folders, $envelope->getFolder()->getName());

        $this->getEnvelopeManager()->delete($envelope, $folders)->flush();

        return $this->redirectResponse($this->path('ccdn_message_message_folder_show',
            array(
                'folderName' => $currentFolder->getName()
            )
        ));
    }
}
