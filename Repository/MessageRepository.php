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
 * MessageRepository
 * 
 * @author Reece Fowell <reece@codeconsortium.com> 
 * @version 1.0
 */
class MessageRepository extends EntityRepository
{
	
	public function findAllPaginatedForFolderById($folder_id, $user_id)
	{
		$query = $this->getEntityManager()
			->createQuery('
				SELECT m
				FROM CCDNMessageMessageBundle:Message m
				WHERE m.folder = :folder_id AND m.sent_to = :user_id
				ORDER BY m.sent_date DESC')
			->setParameters(array('folder_id' => $folder_id, 'user_id' => $user_id));
					
		try {
			return new Pagerfanta(new DoctrineORMAdapter($query));
	        //return $query->getSingleResult();
	    } catch (\Doctrine\ORM\NoResultException $e) {
	        return null;
	    }
	}
	
	public function findMessageByIdForUser($message_id, $user_id)
	{
		$query = $this->getEntityManager()
			->createQuery('
				SELECT m
				FROM CCDNMessageMessageBundle:Message m
				WHERE m.id = :message_id AND m.owned_by = :user_id')
			->setParameters(array('message_id' => $message_id, 'user_id' => $user_id));
			
		try {
	        return $query->getSingleResult();
	    } catch (\Doctrine\ORM\NoResultException $e) {
			return;
	    }
	}
	
	public function findTheseMessagesByUserId($messageIds, $userId)
	{
 		$qb = $this->getEntityManager()->createQueryBuilder();
		$query = $qb->add('select', 'm')
			->from('CCDNMessageMessageBundle:Message', 'm')
			->where($qb->expr()->andx(
				$qb->expr()->eq('m.owned_by', '?1'),
				$qb->expr()->in('m.id', '?2')))
			->setParameters(array('1' => $userId, '2' => array_values($messageIds)))
			->getQuery();
			
		try {
			return $query->getResult();
	    } catch (\Doctrine\ORM\NoResultException $e) {
	        return null;
	    }
	}
}