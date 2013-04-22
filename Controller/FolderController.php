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

use CCDNMessage\MessageBundle\Controller\FolderBaseController;

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
class FolderController extends FolderBaseController
{
    /**
     *
     * @access protected
     * @param  string         $folderName
     * @param  int            $page
     * @return RenderResponse
     */
    public function showFolderByNameAction($folderName, $page)
    {
        $this->isAuthorised('ROLE_USER');

        if ($folderName != 'inbox' && $folderName != 'sent' && $folderName != 'drafts' && $folderName != 'junk' && $folderName != 'trash') {
            $this->isFound(false, 'Folder not found.');
        }

        $folders = $this->getFolderManager()->findAllFoldersForUserById($this->getUser()->getId());
        $currentFolder = $this->getFolderManager()->getCurrentFolder($folders, $folderName);

        $messagesPager = $this->getEnvelopeManager()->findAllPaginatedForFolderById($currentFolder->getId(), $this->getUser()->getId(), $page);

        $crumbs = $this->getCrumbs()
            ->add($this->trans('ccdn_message_message.crumbs.message_index'), $this->path('ccdn_message_message_index'));

        return $this->renderResponse('CCDNMessageMessageBundle:Folder:show.html.',
            array(
                'crumbs' => $crumbs,
                'folders' => $folders,
                'current_folder' => $currentFolder,
                'pager' => $messagesPager,
            )
        );
    }

    /**
     *
     * @access public
     * @param  string           $folderName
     * @return RedirectResponse
     */
    public function folderBulkAction($folderName)
    {
        $this->isAuthorised('ROLE_USER');

        $this->bulkAction();

        return $this->redirectResponse($this->path('ccdn_message_message_folder_show',
            array(
                'folderName' => $folderName)
            )
        );
    }
}
