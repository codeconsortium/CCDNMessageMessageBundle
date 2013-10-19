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

namespace CCDNMessage\MessageBundle\Model\Model;

use Symfony\Component\Security\Core\User\UserInterface;

use CCDNMessage\MessageBundle\Model\Model\BaseModel;
use CCDNMessage\MessageBundle\Model\Model\ModelInterface;

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
class FolderModel extends BaseModel implements ModelInterface
{
    /**
     *
     * @access public
     * @param  int                                          $userId
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findAllFoldersForUserById($userId)
    {
		return $this->getRepository()->findAllFoldersForUserById($userId);
    }

    /**
     *
     * @access public
     * @param  int                                          $folderId
     * @param  int                                          $userId
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findOneFolderByIdAndUserById($folderId, $userId)
    {
		return $this->getRepository()->findOneFolderByIdAndUserById($folderId, $userId);
    }

    /**
     *
     * @access public
     * @param  int     $folderId
     * @param  int     $userId
     * @return array
     */
    public function getReadCounterForFolderById($folderId, $userId)
    {
		return $this->getRepository()->getReadCounterForFolderById($folderId, $userId);
    }

    /**
     *
     * @access public
     * @param  int     $folderId
     * @param  int     $userId
     * @return array
     */
    public function getUnreadCounterForFolderById($folderId, $userId)
    {
		return $this->getRepository()->getUnreadCounterForFolderById($folderId, $userId);
    }
	
    /**
     *
     * @access public
     * @param  \Symfony\Component\Security\Core\User\UserInterface    $user
     * @return \CCDNMessage\MessageBundle\Model\Manager\FolderManager
     */
    public function setupDefaults(UserInterface $user)
    {
        return $this->getManager()->setupDefaults($user);
    }

    /**
     *
     * @access public
     * @param  \Symfony\Component\Security\Core\User\UserInterface    $user
     * @param  array                                                  $folders
     * @return \CCDNMessage\MessageBundle\Model\Manager\FolderManager
     */
    public function updateAllFolderCachesForUser(UserInterface $user, $folders)
    {
        return $this->getManager()->updateAllFolderCachesForUser($user, $folders);
    }

    /**
     *
     * @access public
     * @param  \CCDNMessage\MessageBundle\Entity\Folder               $folder
     * @return \CCDNMessage\MessageBundle\Model\Manager\FolderManager
     */
    public function updateFolderCounterCaches(Folder $folder)
    {
        return $this->getManager()->updateFolderCounterCaches($folder);
    }

    /**
     *
     * @access public
     * @param  array $folders
     * @return int
     */
    public function checkQuotaAllowanceUsed($folders)
    {
        return $this->getManager()->checkQuotaAllowanceUsed($folders);
    }

    /**
     *
     * @access public
     * @param  array                                    $folders
     * @param  string                                   $folderName
     * @return \CCDNMessage\MessageBundle\Entity\Folder
     */
    public function getCurrentFolder($folders, $folderName)
    {
        return $this->getManager()->getCurrentFolder($folders, $folderName);
    }

    /**
     *
     * @access public
     * @param  array $folders
     * @param  int   $quota
     * @return array
     */
    public function getUsedAllowance($folders, $quota)
    {
        return $this->getManager()->getUsedAllowance($folders, $quota);
    }
}