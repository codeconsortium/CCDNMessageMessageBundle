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

namespace CCDNMessage\MessageBundle\Manager;

use CCDNMessage\MessageBundle\Manager\ManagerInterface;
use CCDNMessage\MessageBundle\Manager\BaseManager;

use CCDNMessage\MessageBundle\Entity\Folder;

/**
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
class FolderManager extends BaseManager implements ManagerInterface
{

    /**
     *
     * @access public
     * @param $userId
     * @return $this
     */
    public function setupDefaults($userId)
    {
        $user = $this->container->get('ccdn_user_user.repository.user')->findOneById($userId);

        if (! $user) {
            echo "error, cannot setup PM folders for non-user.";
        }

        $folderNames = array(1 => 'inbox', 2 => 'sent', 3 => 'drafts', 4 => 'junk', 5 => 'trash');

        foreach ($folderNames as $key => $folderName) {
            $folder = new Folder();
            $folder->setOwnedBy($user);
            $folder->setName($folderName);
            $folder->setSpecialType($key);
            $folder->setCachedReadCount(0);
            $folder->setCachedUnreadCount(0);
            $folder->setCachedTotalMessageCount(0);

            $this->persist($folder);
        }

        return $this;
    }

    /**
     *
     * @access public
     * @param $folder
     * @return $this
     */
    public function updateFolderCounterCaches($folder)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        $readCount = $this->container->get('ccdn_message_message.repository.folder')->getReadCounterForFolderById($folder->getId(), $user->getId());
        $readCount = $readCount['readCount'];
        $unreadCount = $this->container->get('ccdn_message_message.repository.folder')->getUnreadCounterForFolderById($folder->getId(), $user->getId());
        $unreadCount = $unreadCount['unreadCount'];
        $totalCount = ($readCount + $unreadCount);

        $folder->setCachedReadCount($readCount);
        $folder->setCachedUnreadCount($unreadCount);
        $folder->setCachedTotalMessageCount($totalCount);

        $this->persist($folder);

        return $this;
    }

    /**
     *
     * @access public
     * @param Array() $folders
     */
    public function checkQuotaAllowanceUsed($folders)
    {
        $totalMessageCount = 0;

        foreach ($folders as $key => $folder) {
            $totalMessageCount += $folder->getCachedTotalMessageCount();
        }

        return $totalMessageCount;
    }

    /**
     *
     * @access public
     * @param Array() $folders, $folderName
     */
    public function getCurrentFolder($folders, $folderName)
    {
        // find the current folder
        $currentFolder = null;

        foreach ($folders as $key => $folder) {
            if ($folder->getName() == $folderName) {
                $currentFolder = $folder;

                break;
            }
        }

        return $currentFolder;
    }

    /**
     *
     * @access public
     * @param Array() $folders, Int $quota
	 * @return Array()
     */
    public function getUsedAllowance($folders, $quota)
    {
        $totalMessageCount = 0;

        foreach ($folders as $key => $folder) {
            $totalMessageCount += $folder->getCachedTotalMessageCount();
        }

        $usedAllowance = ($totalMessageCount / $quota) * 100;

        // where 100 represents 100%, if the number should exceed then reset it to 100%
        if ($usedAllowance > 99) {
            $usedAllowance = 100;
        }

        return array('used_allowance' => $usedAllowance, 'total_message_count' => $totalMessageCount);
    }

}
