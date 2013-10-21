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
 * FolderRepository
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
class FolderRepository extends BaseRepository implements RepositoryInterface
{
	public function findOneFolderForUserByNameAndUserId($folderName, $userId)
	{
        if (null == $folderName || ! is_string($folderName) || $folderName == '') {
            throw new \Exception('Folder Name "' . $folderName . '" is invalid!');
        }

        if (null == $userId || ! is_numeric($userId) || $userId == 0) {
            throw new \Exception('User id "' . $userId . '" is invalid!');
        }

        $params = array(':folderName' => $folderName, ':userId' => $userId);

        $qb = $this->createSelectQuery(array('f'));

        $qb
            ->where('f.name = :folderName')
            ->andWhere('f.ownedByUser = :userId')
        ;

        return $this->gateway->findFolder($qb, $params);
	}

    /**
     *
     * @access public
     * @param  int                                          $userId
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
     * @param  int                                          $folderId
     * @param  int                                          $userId
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
}
