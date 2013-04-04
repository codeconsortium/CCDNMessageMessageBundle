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
			->where('f.ownedByUser = :userId')
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
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function findOneFolderByIdAndUserById($folderId, $userId)
	{
		if (null == $folderId || ! is_numeric($folderId) || $folderId == 0) {
			throw new \Exception('Folder id "' . $folderId . '" is invalid!');
		}
		
		if (null == $userId || ! is_numeric($userId) || $userId == 0) {
			throw new \Exception('User id "' . $userId . '" is invalid!');
		}
		
		$params = array(':folderId' => $folderId, ':userId' => $userId);
		
		$qb = $this->createSelectQuery(array('f'));
		
		$qb
			->where('f.id = :folderId')
			->andWhere('f.ownedByUser = :userId')
			->orderBy('f.specialType', 'ASC')
		;
		
		return $this->gateway->findFolder($qb, $params);
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

		$envelopeEntityClass = $this->managerBag->getEnvelopeManager()->getGateway()->getEntityClass();
			
		$qb
			->select('COUNT(DISTINCT e.id) AS readCount')
			->from($envelopeEntityClass, 'e')
			->where('e.folder = :folderId')
			->andWhere('e.ownedByUser = :userId')
			->andWhere('e.isRead = TRUE')
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

		$envelopeEntityClass = $this->managerBag->getEnvelopeManager()->getGateway()->getEntityClass();
			
		$qb
			->select('COUNT(DISTINCT e.id) AS unreadCount')
			->from($envelopeEntityClass, 'e')
			->where('e.folder = :folderId')
			->andWhere('e.ownedByUser = :userId')
			->andWhere('e.isRead = FALSE')
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
     * @return \CCDNMessage\MessageBundle\Manager\FolderManager
     */
    public function setupDefaults(UserInterface $user)
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
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
	 * @param Array() $folders
     * @return \CCDNMessage\MessageBundle\Manager\FolderManager
     */
    public function updateAllFolderCachesForUser(UserInterface $user, $folders)
    {
        foreach ($folders as $folder) {
            $this->updateFolderCounterCaches($folder);
        }

        $this->flush();

        $this->managerBag->getRegistryManager()->updateCacheUnreadMessagesForUser($user, null, $folders)->flush();

        return $this;
    }
	
    /**
     *
     * @access public
     * @param \CCDNMessage\MessageBundle\Entity\Folder $folder
     * @return \CCDNMessage\MessageBundle\Manager\FolderManager
     */
    public function updateFolderCounterCaches(Folder $folder)
    {
        $readCount = $this->getReadCounterForFolderById($folder->getId(), $folder->getOwnedByUser()->getId());
        $readCount = $readCount['readCount'];
        $unreadCount = $this->getUnreadCounterForFolderById($folder->getId(), $folder->getOwnedByUser()->getId());

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
	 * @return int
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
     * @param array $folders
	 * @param string $folderName
	 * @return \CCDNMessage\MessageBundle\Entity\Folder
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
     * @param array $folders
	 * @param int $quota
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
        if ($usedAllowance > 100) {
            $usedAllowance = 100;
        }

        return array(
			'used_allowance' => $usedAllowance,
			'total_message_count' => $totalMessageCount
		);
    }
}