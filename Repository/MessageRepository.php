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

    public function findAllPaginatedForFolderById($folderId, $userId)
    {
        $query = $this->getEntityManager()
            ->createQuery('
                SELECT m
                FROM CCDNMessageMessageBundle:Message m
                WHERE m.folder = :folderId AND m.ownedBy = :userId
                ORDER BY m.sentDate DESC')
            ->setParameters(array('folderId' => $folderId, 'userId' => $userId));

        try {
            return new Pagerfanta(new DoctrineORMAdapter($query));
            //return $query->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    public function findMessageByIdForUser($messageId, $userId)
    {
        $query = $this->getEntityManager()
            ->createQuery('
                SELECT m
                FROM CCDNMessageMessageBundle:Message m
                WHERE m.id = :messageId AND m.ownedBy = :userId')
            ->setParameters(array('messageId' => $messageId, 'userId' => $userId));

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
                $qb->expr()->eq('m.ownedBy', '?1'),
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
