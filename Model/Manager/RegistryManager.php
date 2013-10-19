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

use Symfony\Component\Security\Core\User\UserInterface;

use CCDNMessage\MessageBundle\Model\Manager\ManagerInterface;
use CCDNMessage\MessageBundle\Model\Manager\BaseManager;

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
class RegistryManager extends BaseManager implements ManagerInterface
{
    /**
     *
     * @access public
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @param \CCDNMessage\MessageBundle\Entity\Registry          $registry
     * @param array                                               $folders
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
