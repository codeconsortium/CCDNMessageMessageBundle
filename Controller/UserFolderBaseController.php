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
 * @category CCDNMessage
 * @package  MessageBundle
 *
 * @author   Reece Fowell <reece@codeconsortium.com>
 * @license  http://opensource.org/licenses/MIT MIT
 * @version  Release: 2.0
 * @link     https://github.com/codeconsortium/CCDNMessageMessageBundle
 *
 */
class UserFolderBaseController extends BaseController
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

        $envelopes = $this->getEnvelopeModel()->findTheseEnvelopesByIdAndByUserId($envelopeIds, $user->getId());

        if ( ! $envelopes || empty($envelopes)) {
            $this->setFlash('notice', $this->trans('flash.post.no_messages_found'));

            return;
        }

        $folders = $this->getFolderHelper()->findAllFoldersForUserById($user);

        $submitAction = $this->getSubmitAction();

        if ($submitAction == 'delete') {
            $this->getEnvelopeModel()->bulkDelete($envelopes, $folders, $user);
        }

        if ($submitAction == 'mark_as_read') {
            $this->getEnvelopeModel()->bulkMarkAsRead($envelopes);
        }

        if ($submitAction == 'mark_as_unread') {
            $this->getEnvelopeModel()->bulkMarkAsUnread($envelopes);
        }

        if ($submitAction == 'move_to') {
            $moveToFolderId = $this->request->get('select_move_to');
            $moveToFolder = $this->getFolderHelper()->filterFolderById($folders, $moveToFolderId);

            if (! is_object($moveToFolder) || ! $moveToFolder instanceof Folder) {
                throw new \Exception('Folder not found.');
            }

            $this->getEnvelopeModel()->bulkMoveToFolder($envelopes, $moveToFolder);
        }

        if ($submitAction == 'send') {
            $this->getMessageModel()->bulkSendDraft($envelopes);
        }
    }
}
