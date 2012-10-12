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

use CCDNMessage\MessageBundle\Manager\ManagerInterface;
use CCDNMessage\MessageBundle\Manager\BaseManager;

use CCDNMessage\MessageBundle\Entity\Folder;
use CCDNMessage\MessageBundle\Entity\Registry;

/**
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
class RegistryManager extends BaseManager implements ManagerInterface
{

    /**
     *
     * @access public
     * @param $user
     */
    public function updateCacheUnreadMessagesForUser($user)
    {
        $folders = $this->container->get('ccdn_message_message.repository.folder')->findAllFoldersForUser($user->getId());

        $totalMessageCount = 0;

        foreach ($folders as $key => $folder) {
            $totalMessageCount += $folder->getCachedUnreadCount();
        }

        $record = $this->container->get('ccdn_message_message.repository.registry')->findRegistryRecordForUser($user->getId());

        if (! $record) {
            $record = new Registry();
            $record->setOwnedBy($user);
        }

        $record->setCachedUnreadMessagesCount($totalMessageCount);

        $this->persist($record)->flush();
    }

}
