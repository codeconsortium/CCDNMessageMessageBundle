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
 * RegistryRepository
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
class RegistryRepository extends BaseRepository implements RepositoryInterface
{
    /**
     *
     * @access public
     * @param  int                                        $userId
     * @return \CCDNMessage\MessageBundle\Entity\Registry
     */
    public function findOneRegistryForUserById($userId)
    {
        if (null == $userId || ! is_numeric($userId) || $userId == 0) {
            throw new \Exception('User id "' . $userId . '" is invalid!');
        }

        $params = array(':userId' => $userId);

        $qb = $this->createSelectQuery(array('r', 'r_owned_by'));

        $qb
            ->leftJoin('r.ownedByUser', 'r_owned_by')
            ->where('r.ownedByUser = :userId')
            ->setParameters($params)
            ->setMaxResults(1);

        return $this->gateway->findRegistry($qb, $params);
    }
}
