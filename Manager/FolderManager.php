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

use Symfony\Component\Security\Core\User\UserInterface;

use CCDNMessage\MessageBundle\Manager\BaseManagerInterface;
use CCDNMessage\MessageBundle\Manager\BaseManager;

use CCDNMessage\MessageBundle\Entity\Folder;

/**
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
class FolderManager extends BaseManager implements BaseManagerInterface
{
	/**
	 *
	 * @access public
	 * @return int
	 */
	public function getMessagesPerPageOnFolders()
	{
		return $this->managerBag->getMessagesPerPageOnFolders();
	}
	
	/**
	 *
	 * @access public
	 * @param int $userId
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function findAllFoldersForUserById($userId)
	{
		if (null == $userId || ! is_numeric($userId) || $userId == 0) {
			throw new \Exception('User id "' . $userId . '" is invalid!');
		}
		
		$params = array(':userId' => $userId);
		
		$qb = $this->createSelectQuery(array('f'));
		
		$qb
			->where('f.ownedBy = :userId')
			->orderBy('f.specialType', 'ASC');
		
		$folders = $this->gateway->findFolders($qb, $params);
		
		if (null == $folders || count($folders) < 1) {
			$this->setupDefaults($userId)->flush();
		
			$folders = $this->findAllFoldersForUserById($userId);
		}
		
		return $folders;
	}
	
	/**
	 *
	 * @access public
	 * @param int $folderId
	 * @param int $userId
	 * @return Array()
	 */	
	public function getReadCounterForFolderById($folderId, $userId)
	{
		if (null == $folderId || ! is_numeric($folderId) || $folderId == 0) {
			throw new \Exception('Folder id "' . $folderId . '" is invalid!');
		}
		
		if (null == $userId || ! is_numeric($userId) || $userId == 0) {
			throw new \Exception('User id "' . $userId . '" is invalid!');
		}
		
		$qb = $this->getQueryBuilder();

		$messageEntityClass = $this->managerBag->getMessageManager()->getGateway()->getEntityClass();
			
		$qb
			->select('COUNT(DISTINCT m.id) AS readCount')
			->from($messageEntityClass, 'm')
			->where('m.folder = :folderId')
			->andWhere('m.ownedBy = :userId')
			->andWhere('m.isRead = TRUE')
			->setParameters(array(':folderId' => $folderId, ':userId'=> $userId));
		
		try {
			return $qb->getQuery()->getSingleResult();			
		} catch (\Doctrine\ORM\NoResultException $e) {
			return array('readCount' => null);
		} catch (\Exception $e) {
			return array('readCount' => null);			
		}
	}
	
	/**
	 *
	 * @access public
	 * @param int $folderId
	 * @param int $userId
	 * @return Array()
	 */	
	public function getUnreadCounterForFolderById($folderId, $userId)
	{
		if (null == $folderId || ! is_numeric($folderId) || $folderId == 0) {
			throw new \Exception('Folder id "' . $folderId . '" is invalid!');
		}
		
		if (null == $userId || ! is_numeric($userId) || $userId == 0) {
			throw new \Exception('User id "' . $userId . '" is invalid!');
		}
		
		$qb = $this->getQueryBuilder();

		$messageEntityClass = $this->managerBag->getMessageManager()->getGateway()->getEntityClass();
			
		$qb
			->select('COUNT(DISTINCT m.id) AS unreadCount')
			->from($messageEntityClass, 'm')
			->where('m.folder = :folderId')
			->andWhere('m.ownedBy = :userId')
			->setParameters(array(':folderId' => $folderId, ':userId'=> $userId));
		
		try {
			return $qb->getQuery()->getSingleResult();			
		} catch (\Doctrine\ORM\NoResultException $e) {
			return array('unreadCount' => null);
		} catch (\Exception $e) {
			return array('unreadCount' => null);			
		}
	}
	
    /**
     *
     * @access public
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @return self
     */
    public function setupDefaults($user)
    {
        if (! is_object($user) || ! $user instanceof UserInterface) {
			$userId = $user;
			
			if (null == $userId || ! is_numeric($userId) || $userId == 0) {
				throw new \Exception('User id "' . $userId . '" is invalid!');
			}
			
			$user = $this->managerBag->getUserProvider()->findOneUserById($userId);
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
     * @return self
     */
    public function updateFolderCounterCaches($folder)
    {
        $user = $this->getUser();

        $readCount = $this->getReadCounterForFolderById($folder->getId(), $user->getId());
        $readCount = $readCount['readCount'];
        $unreadCount = $this->getUnreadCounterForFolderById($folder->getId(), $user->getId());

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
     * @param array $folders
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
     * @param array $folders, $folderName
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
     * @param array $folders, int $quota
	 * @return array
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