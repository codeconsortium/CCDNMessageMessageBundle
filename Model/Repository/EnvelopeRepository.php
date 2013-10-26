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

namespace CCDNMessage\MessageBundle\Model\Repository;

use CCDNMessage\MessageBundle\Model\Repository\BaseRepository;
use CCDNMessage\MessageBundle\Model\Repository\RepositoryInterface;

/**
 * EnvelopeRepository
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
class EnvelopeRepository extends BaseRepository implements RepositoryInterface
{
    /**
     *
     * @access public
     * @param  int                                        $envelopeId
     * @param  int                                        $userId
     * @return \CCDNMessage\MessageBundle\Entity\Envelope
     */
    public function findEnvelopeByIdForUser($envelopeId, $userId)
    {
        if (null == $envelopeId || ! is_numeric($envelopeId) || $envelopeId == 0) {
            throw new \Exception('Envelope id "' . $envelopeId . '" is invalid!');
        }

        if (null == $userId || ! is_numeric($userId) || $userId == 0) {
            throw new \Exception('User id "' . $userId . '" is invalid!');
        }

        $params = array(':envelopeId' => $envelopeId, ':userId' => $userId);

        $qb = $this->createSelectQuery(array('e', 'm', 'e_folder', 'e_owned_by', 'e_recipient', 'm_sender'));

        $qb
            ->join('e.message', 'm')
            ->leftJoin('e.folder', 'e_folder')
            ->leftJoin('e.ownedByUser', 'e_owned_by')
            ->leftJoin('e.sentToUser', 'e_recipient')
            ->leftJoin('m.sentFromUser', 'm_sender')
            ->where('e.id = :envelopeId')
            ->andWhere('e.ownedByUser = :userId')
            ->setParameters($params)
            ->addOrderBy('e.sentDate', 'DESC')
            ->addOrderBy('m.createdDate', 'DESC')
        ;

        return $this->gateway->findEnvelope($qb, $params);
    }

    /**
     *
     * @access public
     * @param  int                                                      $folderId
     * @param  int                                                      $userId
     * @param  int                                                      $page
     * @param  int                                                      $itemsPerPage
     * @return \Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination
     */
    public function findAllEnvelopesForFolderByIdPaginated($folderId, $userId, $page, $itemsPerPage = 25)
    {
        if (null == $folderId || ! is_numeric($folderId) || $folderId == 0) {
            throw new \Exception('Folder id "' . $folderId . '" is invalid!');
        }

        if (null == $userId || ! is_numeric($userId) || $userId == 0) {
            throw new \Exception('User id "' . $userId . '" is invalid!');
        }

        $params = array(':folderId' => $folderId, ':userId' => $userId);

        $qb = $this->createSelectQuery(array('e', 'm', 'e_folder', 'e_owned_by', 'e_recipient', 'm_sender'));

        $qb
            ->join('e.message', 'm')
            ->leftJoin('e.folder', 'e_folder')
            ->leftJoin('e.ownedByUser', 'e_owned_by')
            ->leftJoin('e.sentToUser', 'e_recipient')
            ->leftJoin('m.sentFromUser', 'm_sender')
            ->where('e.folder = :folderId')
            ->andWhere('e.ownedByUser = :userId')
            ->setParameters($params)
            ->addOrderBy('e.sentDate', 'DESC')
            ->addOrderBy('m.createdDate', 'DESC');

        return $this->gateway->paginateQuery($qb, $itemsPerPage, $page);
    }

    /**
     *
     * @access public
     * @param  int                                          $envelopeId
     * @param  int                                          $userId
     * @return \Doctrine\Common\Collections\ArrayCollection
     *
     */
    public function findTheseEnvelopesByIdAndByUserId($envelopeIds, $userId)
    {
        if (null == $userId || ! is_numeric($userId) || $userId == 0) {
            throw new \Exception('User id "' . $userId . '" is invalid!');
        }

        $params = array(':userId' => $userId);

        $qb = $this->createSelectQuery(array('e', 'm', 'e_folder', 'e_owned_by', 'e_recipient', 'm_sender'));

        $qb
            ->join('e.message', 'm')
            ->leftJoin('e.folder', 'e_folder')
            ->leftJoin('e.ownedByUser', 'e_owned_by')
            ->leftJoin('e.sentToUser', 'e_recipient')
            ->leftJoin('m.sentFromUser', 'm_sender')
            ->where($qb->expr()->in('e.id', $envelopeIds))
            ->andWhere('e.ownedByUser = :userId')
            ->setParameters($params)
            ->addOrderBy('e.sentDate', 'DESC')
            ->addOrderBy('m.createdDate', 'DESC')
        ;

        return $this->gateway->findEnvelopes($qb, $params);
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
        if (null == $folderId || ! is_numeric($folderId) || $folderId == 0) {
            throw new \Exception('Folder id "' . $folderId . '" is invalid!');
        }

        if (null == $userId || ! is_numeric($userId) || $userId == 0) {
            throw new \Exception('User id "' . $userId . '" is invalid!');
        }

		$qb = $this->createSelectQuery(array('e'));
		
        $qb
            ->select('COUNT(DISTINCT e.id) AS readCount')
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
     * @param  int     $folderId
     * @param  int     $userId
     * @return array
     */
    public function getUnreadCounterForFolderById($folderId, $userId)
    {
        if (null == $folderId || ! is_numeric($folderId) || $folderId == 0) {
            throw new \Exception('Folder id "' . $folderId . '" is invalid!');
        }

        if (null == $userId || ! is_numeric($userId) || $userId == 0) {
            throw new \Exception('User id "' . $userId . '" is invalid!');
        }

		$qb = $this->createSelectQuery(array('e'));

        $qb
            ->select('COUNT(DISTINCT e.id) AS unreadCount')
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
}
