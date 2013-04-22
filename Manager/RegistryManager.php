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
use CCDNMessage\MessageBundle\Entity\Registry;

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
class RegistryManager extends BaseManager implements BaseManagerInterface
{
    /**
     *
     * @access public
     * @param  int                                        $userId
     * @return \CCDNMessage\MessageBundle\Entity\Registry
     */
    public function findRegistryForUserById($userId)
    {
        if (null == $userId || ! is_numeric($userId) || $userId == 0) {
            throw new \Exception('User id "' . $userId . '" is invalid!');
        }

        $params = array(':userId' => $userId);

        $qb = $this->createSelectQuery(array('r', 'r_owned_by'));

        $qb
            ->leftJoin('r.ownedBy', 'r_owned_by')
            ->where('r.ownedBy = :userId')
            ->setParameters($params)
            ->setMaxResults(1);

        return $this->gateway->findRegistry($qb, $params);
    }

    /**
     *
     * @access public
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @param \CCDNMessage\MessageBundle\Entity\Registry
     * @param Array() $folders
     */
    public function updateCacheUnreadMessagesForUser(UserInterface $user, Registry $registry = null, $folders = null)
    {
        if (null == $registry) {
            $registry = $this->findRegistryForUserById($user->getId());
        }

        if (null == $registry) {
            $registry = new Registry();
            $registry->setOwnedBy($user);
        }

        if (null == $folders) {
            $folders = $this->managerBag->getFolderManager()->findAllFoldersForUserById($user->getId());
        }

        $totalMessageCount = 0;

        foreach ($folders as $key => $folder) {
            $totalMessageCount += $folder->getCachedUnreadCount();
        }

        $registry->setCachedUnreadMessagesCount($totalMessageCount);

        $this->persist($registry)->flush();

        return $this;
    }
}
