<?php

/*
 * This file is part of the CCDN MessageBundle
 *
 * (c) CCDN (c) CodeConsortium <http://www.codeconsortium.com/>
 *
 * Available on github <http://www.github.com/codeconsortium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CCDNMessage\MessageBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * FolderRepository
 *
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
class FolderRepository extends EntityRepository
{

    /**
     *
     * @access public
     * @param int $user_id
     */
    public function findAllFoldersForUser($userId)
    {
        // get topic / post count
        $query = $this->getEntityManager()
            ->createQuery('
                SELECT f
                FROM CCDNMessageMessageBundle:Folder f
                WHERE f.ownedBy = :userId
                ORDER BY f.specialType ASC')
            ->setParameter('userId', $userId);

        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return;
        }
    }

    /**
     *
     * @access public
     * @param int $folder_id
     */
    public function getReadCounterForFolderById($folderId)
    {
        $query = $this->getEntityManager()
            ->createQuery('
                SELECT COUNT(DISTINCT m.id) AS readCount
                FROM CCDNMessageMessageBundle:Message m
                WHERE m.folder = :folderId AND m.isRead = 1
                ')
            ->setParameter('folderId', $folderId);

        try {
            return $query->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return;
        }
    }

    /**
     *
     * @access public
     * @param int $folder_id
     */
    public function getUnreadCounterForFolderById($folderId)
    {
        $query = $this->getEntityManager()
            ->createQuery('
                SELECT COUNT(DISTINCT m.id) AS unreadCount
                FROM CCDNMessageMessageBundle:Message m
                WHERE m.folder = :folderId AND m.isRead = 0
                ')
            ->setParameter('folderId', $folderId);

        try {
            return $query->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return;
        }
    }
}
