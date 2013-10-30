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

use CCDNMessage\MessageBundle\Controller\UserFolderBaseController;

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
class UserFolderController extends UserFolderBaseController
{
    /**
     *
     * @access protected
     * @param  string         $folderName
     * @return RenderResponse
     */
    public function showFolderByNameAction($folderName)
    {
        $this->isAuthorised('ROLE_USER');
        $folders = $this->getFolderHelper()->findAllFoldersForUserById($this->getUser());
        $this->isFound($currentFolder = $this->getFolderHelper()->filterFolderByName($folders, $folderName));
        $messagesPager = $this->getEnvelopeModel()->findAllEnvelopesForFolderByIdPaginated($currentFolder->getId(), $this->getUser()->getId(), $this->getQuery('page', 1), 25);

        return $this->renderResponse('CCDNMessageMessageBundle:User:Folder/show.html.', array(
            'crumbs' => $this->getCrumbs()->addUserFolderShow($currentFolder),
            'folders' => $folders,
            'current_folder' => $currentFolder,
            'pager' => $messagesPager,
        ));
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

        return $this->redirectResponse($this->path('ccdn_message_message_user_folder_show', array('folderName' => $folderName)));
    }
}
