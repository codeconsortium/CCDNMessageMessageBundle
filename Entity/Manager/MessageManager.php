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

use CCDNMessage\MessageBundle\Entity\Message;

/**
 * 
 * @author Reece Fowell <reece@codeconsortium.com> 
 * @version 1.0
 */
class MessageManager extends BaseManager implements EntityManagerInterface
{
	
	
	/**
	 *
	 * @access public
	 * @param $message
	 * @return $this
	 */
	public function insert($message)
	{
		$recipients = $message->getSendTo();
		
		if ($recipients = preg_split('/((,)|(\s))/', $recipients, PREG_OFFSET_CAPTURE))
		{
			foreach ($recipients as $key => $recipient)
			{			
				$recipients[$key] = preg_replace("/[^a-zA-Z0-9_]/", "", $recipients[$key]);

				if ( ! $recipient)
				{
					unset($recipients[$key]);
				}
			}				

			$sendToUsers = $this->container->get('user.repository')->findTheseUsersByUsername($recipients);				
		} else {
			$recipients = array($value);
			
			$sendToUsers = $this->container->get('user.repository')->findByUsername($recipients);
		}

		$user = $this->container->get('security.context')->getToken()->getUser();
		$sendToUsers[] = $user; // send to self so we have it in our sent folder!
		
		$senderAlreadyHasCC = false;
		
		foreach($sendToUsers as $recipient_key => $recipient)
		{
			$folders = $this->container->get('folder.repository')->findAllFoldersForUser($recipient->getId());

			if ( ! $folders)
			{
				$this->container->get('folder.manager')->setupDefaults($recipient->getId())->flushNow();

				$folders = $this->container->get('folder.repository')->findAllFoldersForUser($recipient->getId());		        
			}

			$temp = new Message();
			$temp->setSentTo($recipient);
			$temp->setSendTo($message->getSendTo());
			$temp->setSentFrom($user);
			$temp->setSubject($message->getSubject());
			$temp->setBody($message->getBody());
			$temp->setSentDate($message->getSentDate());
			$temp->setCreatedDate($message->getCreatedDate());
			$temp->setIsDraft($message->getIsDraft());
			$temp->setOwnedBy($recipient);
			$temp->setReadIt(false);
			$temp->setFlagged($message->getFlagged());
			$temp->setAttachment($message->getAttachment());
			
			if ($recipient->getUsername() == $user->getUsername() && ! $senderAlreadyHasCC)
			{
//				$folderCachesToBeUpdated[] = $folders[1]; // 1 is inbox
				$temp->setFolder($folders[1]);
				$senderAlreadyHasCC = true;
				$temp->setReadIt(true);
			} else {
//				$folderCachesToBeUpdated[] = $folders[0]; // 2 is sent box
				$temp->setFolder($folders[0]);				
			}
			
			$this->persist($temp);			
		}

		$this->flushNow();


		foreach($sendToUsers as $recipient)
		{
			$this->updateAllFolderCachesForUser($user);		
		}
//		$folderManager = $this->container->get('folder.manager');
//		foreach ($folderCachesToBeUpdated as $folder)
//		{
//			$folderManager->updateFolderCounterCaches($folder);			
//		}
//		$folderManager->flushNow();
				
		return $this;
	}
	
	
	/**
	 *
	 * @access public
	 * @param $message
	 * @return $this
	 */
	public function markAsRead($message)
	{
		$message->setReadIt(true);
		$this->persist($message)->flushNow();
		
//		$folderManager = $this->container->get('folder.manager');
//		$folderManager->updateFolderCounterCaches($message->getFolder())->flushNow();
		
		return $this;
	}
	
	
	/**
	 *
	 * @access public
	 * @param $message
	 * @return $this
	 */
	public function markAsUnread($message)
	{
		$message->setReadIt(false);
		$this->persist($message)->flushNow();

//		$folderManager = $this->container->get('folder.manager');
//		$folderManager->updateFolderCounterCaches($message->getFolder())->flushNow();
		
		return $this;
	}
	
	
	/**
	 *
	 * @access public
	 * @param $message, $folders
	 * @return $this
	 */
	public function delete($message, $folders)
	{
//		$folderCachesToBeUpdated = array();
		
		if ($message->getFolder()->getName() == 'trash')
		{
//			$folderCachesToBeUpdated[] = $message->getFolder();
			
			$this->remove($message);
		} else {
			foreach($folders as $folder)
			{
				if ($folder->getName() == 'trash')
				{
					$message->setFolder($folder);
					
//					$folderCachesToBeUpdated[] = $folder;
					
					break;
				}
			}
			
			$this->persist($message);
		}

//		$this->flushNow();
			
//		$folderManager = $this->container->get('folder.manager');
//		foreach ($folderCachesToBeUpdated as $folder)
//		{
//			$folderManager->updateFolderCounterCaches($folder);			
//		}
//		$folderManager->flushNow();
				
		return $this;
	}
	
	
	/**
	 *
	 * @access public
	 * @param $messages, $folders
	 * @return $this
	 */
	public function bulkDelete($messages, $folders)
	{	
		// find the trash folder
		foreach($folders as $folder)
		{
			if ($folder->getName() == 'trash')
			{
				$trash = $folder;
				
				break;
			}
		}

		// trash or remove each message
		foreach ($messages as $message)
		{
			if ($message->getFolder()->getName() == 'trash')
			{
				$this->remove($message);
			} else {
				$message->setFolder($trash);
				$this->persist($message);
			}			
		}
		
//		$this->flushNow();
		
//		$folderManager = $this->container->get('folder.manager');
//		foreach ($folders as $folder)
//		{
//			$folderManager->updateFolderCounterCaches($folder);			
//		}
//		$folderManager->flushNow();
		
		return $this;
	}
	
	
	/**
	 *
	 * @access public
	 * @param $messages
	 * @return $this
	 */
	public function bulkMarkAsRead($messages)
	{
		foreach ($messages as $message)
		{
			$message->setReadIt(true);
			$this->persist($message);
		}
		
//		$this->flushNow();
		
//		$folderManager = $this->container->get('folder.manager');
//		$folderManager->updateFolderCounterCaches($messages[0]->getFolder())->flushNow();
		
		return $this;
	}
	
	
	/**
	 *
	 * @access public
	 * @param $messages
	 * @return $this
	 */
	public function bulkMarkAsUnread($messages)
	{
		foreach ($messages as $message)
		{
			$message->setReadIt(false);
			$this->persist($message);
		}
		
//		$this->flushNow();
		
//		$folderManager = $this->container->get('folder.manager');
//		$folderManager->updateFolderCounterCaches($messages[0]->getFolder())->flushNow();
			
		return $this;		
	}
	
	
	/**
	 *
	 * @access public
	 * @param $messages, $moveTo
	 * @return $this
	 */
	public function bulkMoveToFolder($messages, $moveTo)
	{
//		$oldFolder = $messages[0]->getFolder();
		
		foreach ($messages as $message)
		{
			$message->setFolder($moveTo);
			$this->persist($message);
		}
		
//		$this->flushNow();
		
//		$folderManager = $this->container->get('folder.manager');
//		$folderManager->updateFolderCounterCaches($oldFolder)		
//			->updateFolderCounterCaches($moveTo)
//			->flushNow();
		
		return $this;
	}
	
	
	/**
	 *
	 * @access public
	 * @param $user
	 * @return $this
	 */
	public function updateAllFolderCachesForUser($user)
	{		
		$folders = $this->container->get('folder.repository')->findAllFoldersForUser($user->getId());

		$folderManager = $this->container->get('folder.manager');
		
		foreach($folders as $folder)
		{
			$folderManager->updateFolderCounterCaches($folder);		
		}

		$folderManager->flushNow();
			
		return $this;
	}
	
}