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

namespace CCDNMessage\MessageBundle\Entity\Manager;

use CCDNComponent\CommonBundle\Entity\Manager\EntityManagerInterface;
use CCDNComponent\CommonBundle\Entity\Manager\BaseManager;

use CCDNMessage\MessageBundle\Entity\Folder;
use CCDNMessage\MessageBundle\Entity\Registry;

/**
 * 
 * @author Reece Fowell <reece@codeconsortium.com> 
 * @version 1.0
 */
class RegistryManager extends BaseManager implements EntityManagerInterface
{
	
	
	/**
	 *
	 * @access public
	 * @param $user_id
	 * @return $this
	 */	
	public function setupDefaults($user_id)
	{
		$user = $this->container->get('ccdn_user_user.user.repository')->findOneById($user_id);
		
		if ( ! $user)
		{
			echo "error, cannot setup PM folders for non-user.";
		}
		
		$folderNames = array(1 => 'inbox', 2 => 'sent', 3 => 'drafts', 4 => 'junk', 5 => 'trash');
		
		foreach($folderNames as $key => $folderName)
		{
			$folder = new Folder();
			$folder->setOwnedBy($user);
			$folder->setName($folderName);
			$folder->setSpecialType($key);
			$folder->setCacheReadCount(0);
			$folder->setCacheUnreadCount(0);
			$folder->setCacheTotalMessageCount(0);
			
			$this->persist($folder);
		}
		
		return $this;
	}


	public function updateCacheUnreadMessagesForUser($user)
	{
		$folders = $this->container->get('ccdn_message_message.folder.repository')->findAllFoldersForUser($user->getId());
		
		$totalMessageCount = 0;

		foreach($folders as $key => $folder)
		{
			$totalMessageCount += $folder->getCacheUnreadCount();
		}

		$record = $this->container->get('ccdn_message_message.registry.repository')->findRegistryRecordForUser($user->getId());
		
		if ( ! $record)
		{
			$record = new Registry();
			$record->setOwnedBy($user);
		}
		
		$record->setCacheUnreadMessagesCount($totalMessageCount);
		
		$this->persist($record)->flushNow();
	}
	
}