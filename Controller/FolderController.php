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
class FolderController extends ContainerAware
{

    /**
     *
     * @access protected
     * @param  String $folderName, Int $page
     * @return RenderResponse
     */
    public function showFolderByNameAction($folderName, $page)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException('You do not have access to this section.');
        }

        if ($folderName != 'inbox' && $folderName != 'sent'
        && $folderName != 'drafts' && $folderName != 'junk' && $folderName != 'trash')
        {
            throw new NotFoundHttpExceptin('Folder not found.');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $folders = $this->container->get('ccdn_message_message.folder.repository')->findAllFoldersForUser($user->getId());

        $folderManager = $this->container->get('ccdn_message_message.folder.manager');

        if (! $folders) {
            $folderManager->setupDefaults($user->getId())->flush();

            $folders = $this->container->get('ccdn_message_message.folder.repository')->findAllFoldersForUser($user->getId());
        }

        $currentFolder = $folderManager->getCurrentFolder($folders, $folderName);

        $messagesPager = $this->container->get('ccdn_message_message.message.repository')->findAllPaginatedForFolderById($currentFolder, $user->getId());

        $messagesPerPage = $this->container->getParameter('ccdn_message_message.folder.show.messages_per_page');
        $messagesPager->setMaxPerPage($messagesPerPage);
        $messagesPager->setCurrentPage($page, false, true);

        $messages = $messagesPager->getCurrentPageResults();

        $quota = $this->container->getParameter('ccdn_message_message.quotas.max_messages');

        $stats = $folderManager->getUsedAllowance($folders, $quota);

		//$formHandler = $this->container->get('ccdn_message_message.message_manager.form.handler');
		//$formHandler->setDefaultValues(array('messages' => $messages));
		
        $crumbs = $this->container->get('ccdn_component_crumb.trail')
            ->add($this->container->get('translator')->trans('ccdn_message_message.crumbs.message_index', array(), 'CCDNMessageMessageBundle'), $this->container->get('router')->generate('ccdn_message_message_index'), "home");

        return $this->container->get('templating')->renderResponse('CCDNMessageMessageBundle:Folder:show.html.' . $this->getEngine(), array(
            'user_profile_route' => $this->container->getParameter('ccdn_message_message.user.profile_route'),
            'crumbs' => $crumbs,
			//'form'	=> $formHandler->getForm()->createView(),
            'pager' => $messagesPager,
            'folders' => $folders,
            'current_folder' => $currentFolder,
            'messages' => $messages,
            'quota' => $quota,
            'used_allowance' => $stats['used_allowance'],
            'total_message_count' => $stats['total_message_count'],
        ));
    }

    /**
     *
     * @access public
     * @return RedirectResponse
     */
    public function bulkAction()
    {

        if ( ! $this->container->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException('You do not have access to this section.');
        }

        //
        // Get all the message id's.
        //
        $messageIds = array();
        $ids = $_POST;
        foreach ($ids as $messageKey => $messageId) {
            if (substr($messageKey, 0, 14) == 'check_message_') {
                //
                // Cast the key values to int upon extraction.
                //
                $id = (int) substr($messageKey, 14, (strlen($messageKey) - 14));

                if (is_int($id) == true) {
                    $messageIds[] = $id;
                }
            }
        }

        //
        // Don't bother if there are no messages to process.
        //
        if (count($messageIds) < 1) {
            return new RedirectResponse($this->container->get('router')->generate('ccdn_message_message_index'));
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $messages = $this->container->get('ccdn_message_message.message.repository')->findTheseMessagesByUserId($messageIds, $user->getId());

        if ( ! $messages || empty($messages)) {
            throw new NotFoundHttpException('No messages found!');
        }

        $folder = $messages[0]->getFolder();

        $action = $_POST['submit_action'];

        if ($action == 'submit_delete' || isset($_POST['submit_delete'])) {
            $folders = $this->container->get('ccdn_message_message.folder.repository')->findAllFoldersForUser($user->getId());

            $this->container->get('ccdn_message_message.message.manager')->bulkDelete($messages, $folders)->flush();
        }
        if ($action == 'submit_mark_as_read' || isset($_POST['submit_mark_as_read'])) {
            $this->container->get('ccdn_message_message.message.manager')->bulkMarkAsRead($messages)->flush();
        }
        if ($action == 'submit_mark_as_unread' || isset($_POST['submit_mark_as_unread'])) {
            $this->container->get('ccdn_message_message.message.manager')->bulkMarkAsUnread($messages)->flush();
        }
        if ($action == 'submit_move_to' || isset($_POST['submit_move_to'])) {
            $moveTo = $this->container->get('ccdn_message_message.folder.repository')->findOneById($_POST['select_move_to']);
            $this->container->get('ccdn_message_message.message.manager')->bulkMoveToFolder($messages, $moveTo)->flush();
        }
        if ($action == 'submit_send' || isset($_POST['submit_send'])) {
            $this->container->get('ccdn_message_message.message.manager')->sendDraft($messages)->flush();
        }

        $this->container->get('ccdn_message_message.message.manager')->updateAllFolderCachesForUser($user);

        return new RedirectResponse($this->container->get('router')->generate('ccdn_message_message_folder_show', array('folder_name' => $folder->getName())));

//		$session = $this->container->get('request')->getSession();
//
//		if ($session->has('referer'))
//		{
//			return new RedirectResponse($session->get('referer'));
//		} else {
//			return new RedirectResponse($this->container->get('router')->generate('ccdn_message_message_index')); // if no referer then go to inbox
//		}
    }

    /**
     *
     * @access protected
     * @return string
     */
    protected function getEngine()
    {
        return $this->container->getParameter('ccdn_message_message.template.engine');
    }

}
