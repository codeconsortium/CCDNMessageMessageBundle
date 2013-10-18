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

namespace CCDNMessage\MessageBundle\Model\Manager;

use CCDNMessage\MessageBundle\Model\Manager\BaseManagerInterface;
use CCDNMessage\MessageBundle\Model\Manager\BaseManager;

/**
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
class UserManager extends BaseManager implements BaseManagerInterface
{
    /**
     *
     * @access public
     * @param  int                                                 $userId
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    public function findOneUserById($userId)
    {
        if (null == $userId || ! is_numeric($userId) || $userId == 0) {
            throw new \Exception('User ID "' . $userId . '" is invalid!');
        }

        $params = array(':userId' => $userId);

        $qb = $this->createSelectQuery(array('u'));

        $qb
            ->select('u')
            ->from($this->entityClass, 'u')
            ->where('u.id = :userId')
            ->setMaxResults(1)
        ;

        return $this->gateway->findUser($qb, $params);
    }

    /**
     *
     * @access public
     * @param  string                                              $username
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    public function findOneUserByUsername($username)
    {
        if (null == $username || ! is_string($username) || $username == 0) {
            throw new \Exception('Username "' . $username . '" is invalid!');
        }

        $params = array(':username' => $username);

        $qb = $this->createSelectQuery(array('u'));

        $qb
            ->where('u.username = :username')
            ->setMaxResults(1)
        ;

        return $this->gateway->findUser($qb, $params);
    }

    /**
     *
     * @access public
     * @param  Array()                                      $usernames
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findTheseUsersByUsername(array $usernames = array())
    {
        if (null == $usernames || count($usernames) < 1) {
            throw new \Exception('Username "' . $userId . '" is invalid!');
        }

        $params = array();

        $qb = $this->createSelectQuery(array('u'));

        $qb
            ->where(
                $qb->expr()->in('u.username', $usernames)
            )
        ;

        return $this->gateway->findUsers($qb, $params);
    }
}
