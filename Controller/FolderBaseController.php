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

use CCDNMessage\MessageBundle\Entity\Folder;

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
		$envelopeIds = $this->getCheckedItemIds('envelope');
		
        // Don't bother if there are no checkboxes to process.
        if (count($envelopeIds) < 1) {
            return;
        }

        $user = $this->getUser();

		$envelopes = $this->getEnvelopeManager()->findTheseEnvelopesByIdAndByUserId($envelopeIds, $user->getId());

        if ( ! $envelopes || empty($envelopes)) {
            $this->setFlash('notice', $this->trans('flash.post.no_messages_found'));

            return;
        }

		$folders = $this->getFolderManager()->findAllFoldersForUserById($user->getId());

		$submitAction = $this->getSubmitAction();
		
        if ($submitAction == 'delete') {
            $this->getEnvelopeManager()->bulkDelete($envelopes, $folders, $user)->flush();
        }
		
        if ($submitAction == 'mark_as_read') {
			$this->getEnvelopeManager()->bulkMarkAsRead($envelopes, $folders, $user)->flush();
        }
		
        if ($submitAction == 'mark_as_unread') {
			$this->getEnvelopeManager()->bulkMarkAsUnread($envelopes, $folders, $user)->flush();
        }
		
        if ($submitAction == 'move_to') {
			$moveToFolderId = $this->request->get('select_move_to');
            $moveToFolder = $this->getFolderManager()->findOneFolderByIdAndUserById($moveToFolderId, $user->getId());

			if (! is_object($moveToFolder) || ! $moveToFolder instanceof Folder) {
				throw new \Exception('Folder not found.');
			}
			
            $this->getEnvelopeManager()->bulkMoveToFolder($envelopes, $folders, $moveToFolder, $user)->flush();
        }

        if ($submitAction == 'send') {
			$this->getMessageManager()->bulkSendDraft($envelopes)->flush();
        }
    }
}