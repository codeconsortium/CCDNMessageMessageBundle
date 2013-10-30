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

namespace CCDNMessage\MessageBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use CCDNMessage\MessageBundle\Entity\Registry;
use CCDNMessage\MessageBundle\Entity\Folder;
use CCDNMessage\MessageBundle\Entity\Message;

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
class LoadMessageData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     *
     * @access public
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
//		//
//		// Registry.
//		//
//		$registry = new Registry();
//
//		$registry->setOwnedByUser($manager->merge($this->getReference('user-admin')));
//		$registry->setCachedUnreadMessageCount(1);
//
//		$manager->persist($registry);
//		$manager->flush();
//
//		//
//		// Setup Folders.
//		//
//		$folderNames = array(1 => 'inbox', 2 => 'sent', 3 => 'drafts', 4 => 'junk', 5 => 'trash');
//		$inbox = null;
//
//        foreach ($folderNames as $key => $folderName) {
//            $folder = new Folder();
//            $folder->setOwnedByUser($manager->merge($this->getReference('user-admin')));
//            $folder->setName($folderName);
//            $folder->setSpecialType($key);
//            $folder->setCachedReadCount(0);
//            $folder->setCachedUnreadCount(($folderName == 'inbox') ? 1 : 0);
//            $folder->setCachedTotalMessageCount(($folderName == 'inbox') ? 1 : 0);
//
//            $manager->persist($folder);
//
//			if ($folderName == 'inbox') { $inbox = $folder; }
//        }
//
//		$manager->flush();
//		$manager->refresh($inbox);
//
//		//
//		// Message.
//		//
//		$message = new Message();
//
//		$message->setFolder($inbox);
//		$message->setOwnedByUser($manager->merge($this->getReference('user-admin')));
//		$message->setSentTo($manager->merge($this->getReference('user-admin')));
//		$message->setSendTo($manager->merge($this->getReference('user-admin')));
//		$message->setSentFrom($manager->merge($this->getReference('user-test')));
//		$message->setCreatedDate(new \DateTime());
//		$message->setSentDate(new \DateTime());
//
//		$message->setSubject('Welcome Message.');
//		$message->setBody('Welcome to CodeConsortium! This is the private messaging system, use this to send and receive messages to other users.<br><br>Check back here from time to time to see if you have any new messages.');
//		$message->setRead(false);
//		$message->setFlagged(true);
//		$message->setDraft(false);
//
//		$manager->persist($message);
//		$manager->flush();
    }

    /**
     *
     * @access public
     * @return int
     */
    public function getOrder()
    {
        return 3;
    }
}
