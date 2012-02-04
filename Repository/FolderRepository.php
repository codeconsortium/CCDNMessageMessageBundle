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
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

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
	public function findAllFoldersForUser($user_id)
	{
		// get topic / post count
		$query = $this->getEntityManager()
			->createQuery('	
				SELECT f 
				FROM CCDNMessageMessageBundle:Folder f
				WHERE f.owned_by = :id
				ORDER BY f.special_type ASC')
			->setParameter('id', $user_id);

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
	public function getReadCounterForFolderById($folder_id)
	{
		$query = $this->getEntityManager()
			->createQuery('	
				SELECT COUNT(DISTINCT m.id) AS readCount
				FROM CCDNMessageMessageBundle:Message m
				WHERE m.folder = :id AND m.read_it = 1
				')
			->setParameter('id', $folder_id);

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
	public function getUnreadCounterForFolderById($folder_id)
	{
		$query = $this->getEntityManager()
			->createQuery('	
				SELECT COUNT(DISTINCT m.id) AS unreadCount
				FROM CCDNMessageMessageBundle:Message m
				WHERE m.folder = :id AND m.read_it = 0
				')
			->setParameter('id', $folder_id);

		try {
	        return $query->getSingleResult();
	    } catch (\Doctrine\ORM\NoResultException $e) {
	        return;
	    }
	}
}