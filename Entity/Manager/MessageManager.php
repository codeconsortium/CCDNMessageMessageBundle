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
		
		// build a list of recipients from the sendTo field.
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

		// add ourself to the sending list so we have a carbon-copy.
		$user = $this->container->get('security.context')->getToken()->getUser();
		$sendToUsers[] = $user; // send to self so we have it in our sent folder!
		
		// a check for when we encounter our own folder, in which
		// case the message goes into outbox instead of inbox.
		$senderAlreadyHasCC = false;
		
		$folderRepo = $this->container->get('folder.repository');
		$folderManager = $this->container->get('folder.manager');
		$quota = $this->container->getParameter('ccdn_message_message.quotas.max_messages');
		
		foreach($sendToUsers as $recipient_key => $recipient)
		{
			$folders = $folderRepo->findAllFoldersForUser($recipient->getId());

			if ( ! $folders)
			{
				$folderManager->setupDefaults($recipient->getId())->flushNow();

				$folders = $folderRepo->findAllFoldersForUser($recipient->getId());		        
			}

			$used = $folderManager->checkQuotaAllowanceUsed($folders);
			
			if ($used >= $quota)
			{
				$this->container->get('session')->setFlash('notice_' . $recipient, 
					$this->container->get('translator')->trans('flash.message.send.inbox_full', array('%user%' => $recipient->getUsername()), 'CCDNMessageMessageBundle'));
				
				continue;
			} else {
				if ($recipient->getUsername() != $user->getUsername())
				{
					$this->container->get('session')->setFlash('notice_' . $recipient,
						$this->container->get('translator')->trans('flash.message.sent.success', array('%user%' => $recipient->getUsername()), 'CCDNMessageMessageBundle'));
				}
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
				$temp->setFolder($folders[1]);
				$senderAlreadyHasCC = true;
				$temp->setReadIt(true);
			} else {
				$temp->setFolder($folders[0]);				
			}
			
			
			$this->persist($temp);	
		}

		$this->flushNow();


		foreach($sendToUsers as $recipient)
		{
			$this->updateAllFolderCachesForUser($recipient);		
		}
				
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
		
		if ($message->getFolder()->getName() == 'trash')
		{
			$this->remove($message);
		} else {
			foreach($folders as $folder)
			{
				if ($folder->getName() == 'trash')
				{
					$message->setFolder($folder);
					
					break;
				}
			}
			
			$this->persist($message);
		}

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
		
		foreach ($messages as $message)
		{
			$message->setFolder($moveTo);
			$this->persist($message);
		}
		
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
			
		$this->container->get('registry.manager')->updateCacheUnreadMessagesForUser($user);
		
		return $this;
	}
	
}