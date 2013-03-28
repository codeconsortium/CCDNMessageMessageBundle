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

use CCDNMessage\MessageBundle\Controller\BaseController;

/**
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
class FolderBaseController extends BaseController
{
    /**
     *
     * @access protected
     */
    protected function bulkAction()
    {
		$messageIds = $this->getCheckedItemIds('message_');
		
        // Don't bother if there are no checkboxes to process.
        if (count($messageIds) < 1) {
            return;
        }

        $user = $this->getUser();

        //$posts = $this->getPostManager()->findThesePostsById($itemIds);
		$messages = $this->container->get('ccdn_message_message.repository.message')->findTheseMessagesByUserId($messageIds, $user->getId());

        if ( ! $messages || empty($messages)) {
            $this->setFlash('notice', $this->trans('flash.post.no_messages_found'));

            return;
        }

		$submitAction = $this->getSubmitAction();
		
        if ($submitAction == 'delete') {
            $folders = $this->container->get('ccdn_message_message.repository.folder')->findAllFoldersForUser($user->getId());

            $this->getMessageManager()->bulkDelete($messages, $folders)->flush();
        }
        if ($submitAction == 'mark_as_read') {
			$this->getMessageManager()->bulkMarkAsRead($messages)->flush();
        }
        if ($submitAction == 'mark_as_unread') {
			$this->getMessageManager()->bulkMarkAsUnread($messages)->flush();
        }
        if ($submitAction == 'move_to') {
            $moveTo = $this->container->get('ccdn_message_message.repository.folder')->findOneById($_POST['select_move_to']);
            $this->getMessageManager()->bulkMoveToFolder($messages, $moveTo)->flush();
        }
        if ($submitAction == 'send') {
			$this->getMessageManager()->sendDraft($messages)->flush();
        }

        $this->container->get('ccdn_message_message.manager.message')->updateAllFolderCachesForUser($user);
    }
}