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
 * ThreadRepository
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
class ThreadRepository extends BaseRepository implements RepositoryInterface
{
    /**
     *
     * @access public
     * @param  int                                        $envelopeId
     * @param  int                                        $userId
     * @return \CCDNMessage\MessageBundle\Entity\Envelope
     */
    public function findThreadWithAllEnvelopesByThreadIDAndUserId($threadId, $userId)
    {
        if (null == $threadId || ! is_numeric($threadId) || $threadId == 0) {
            throw new \Exception('Thread id "' . $threadId . '" is invalid!');
        }

        if (null == $userId || ! is_numeric($userId) || $userId == 0) {
            throw new \Exception('User id "' . $userId . '" is invalid!');
        }

        $params = array(':threadId' => $threadId, ':userId' => $userId);

        $qb = $this->createSelectQuery(array('t', 'e', 'm', 'e_folder', 'e_owned_by', 'm_sender', 'm_recipient'));

        $qb
            ->leftJoin('t.envelopes', 'e')
            ->leftJoin('e.message', 'm')
            ->leftJoin('e.folder', 'e_folder')
            ->leftJoin('e.ownedByUser', 'e_owned_by')
            ->leftJoin('m.sentFromUser', 'm_sender')
            ->leftJoin('m.sentToUser', 'm_recipient')
            ->where('t.id = :threadId')
            ->andWhere('e.ownedByUser = :userId')
            ->setParameters($params)
            ->addOrderBy('e.sentDate', 'DESC')
            ->addOrderBy('m.createdDate', 'DESC')
        ;

        return $this->gateway->findThread($qb, $params);
    }
}
