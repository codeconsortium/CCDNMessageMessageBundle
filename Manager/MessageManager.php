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

use CCDNMessage\MessageBundle\Entity\Message;

/**
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
class MessageManager extends BaseManager implements BaseManagerInterface
{
	/**
	 *
	 * @access public
	 * @return int
	 */
	public function getMessagesPerPageOnFolders()
	{
		return $this->managerBag->getMessagesPerPageOnFolders();
	}
	
	/**
	 *
	 * @access public
	 * @return int
	 */
	public function getQuotaMaxAllowanceForMessages()
	{
		return $this->managerBag->getQuotaMaxAllowanceForMessages();
	}
	
	/**
	 *
	 * @access public
	 * @param int $topicId
	 * @return \CCDNMessage\MessageBundle\Entity\Message
	 */	
	public function findMessageByIdForUser($messageId, $userId)
	{
		if (null == $messageId || ! is_numeric($messageId) || $messageId == 0) {
			throw new \Exception('Message id "' . $messageId . '" is invalid!');
		}
		
		if (null == $userId || ! is_numeric($userId) || $userId == 0) {
			throw new \Exception('User id "' . $userId . '" is invalid!');
		}
		
		$params = array(':messageId' => $messageId, ':userId' => $userId);
		
		$qb = $this->createSelectQuery(array('m', 'm_folder', 'm_response', 'm_sender', 'm_recipient', 'm_owned_by', 'm_response_folder', 'm_response_sender', 'm_response_recipient', 'm_response_owned_by'));
		
		$qb
			->join('m.folder', 'm_folder')
			->leftJoin('m.inResponseTo', 'm_response')
				->leftJoin('m_response.folder', 'm_response_folder')
				->leftJoin('m_response.sentTo', 'm_response_recipient')
				->leftJoin('m_response.ownedBy', 'm_response_owned_by')
				->leftJoin('m_response.sentFrom', 'm_response_sender')
			->leftJoin('m.sentFrom', 'm_sender')
			->leftJoin('m.sentTo', 'm_recipient')
			->leftJoin('m.ownedBy', 'm_owned_by')
			->where('m.id = :messageId')
			->andWhere('m.ownedBy = :userId')
			->setParameters($params)
			->addOrderBy('m.sentDate', 'DESC')
			->addOrderBy('m.createdDate', 'DESC')
			->setMaxResults(1);
		
		return $this->gateway->findMessage($qb, $params);
	}
	
	/**
	 *
	 * @access public
	 * @param int $folderId
	 * @param int $userId
	 * @param int $page
	 * @return \Pagerfanta\Pagerfanta
	 */	
	public function findAllPaginatedForFolderById($folderId, $userId, $page)
	{
		if (null == $folderId || ! is_numeric($folderId) || $folderId == 0) {
			throw new \Exception('Folder id "' . $folderId . '" is invalid!');
		}
		
		if (null == $userId || ! is_numeric($userId) || $userId == 0) {
			throw new \Exception('User id "' . $userId . '" is invalid!');
		}
			
		$params = array(':folderId' => $folderId, ':userId' => $userId);
		
		$qb = $this->createSelectQuery(array('m', 'm_folder', 'm_response', 'm_sender', 'm_recipient', 'm_owned_by', 'm_response_folder', 'm_response_sender', 'm_response_recipient', 'm_response_owned_by'));
		
		$qb
			->join('m.folder', 'm_folder')
			->leftJoin('m.inResponseTo', 'm_response')
				->leftJoin('m_response.folder', 'm_response_folder')
				->leftJoin('m_response.sentTo', 'm_response_recipient')
				->leftJoin('m_response.ownedBy', 'm_response_owned_by')
				->leftJoin('m_response.sentFrom', 'm_response_sender')
			->leftJoin('m.sentFrom', 'm_sender')
			->leftJoin('m.sentTo', 'm_recipient')
			->leftJoin('m.ownedBy', 'm_owned_by')
			->where('m.folder = :folderId')
			->andWhere('m.ownedBy = :userId')
			->setParameters($params)
			->addOrderBy('m.sentDate', 'DESC')
			->addOrderBy('m.createdDate', 'DESC');

		return $this->gateway->paginateQuery($qb, $this->getMessagesPerPageOnFolders(), $page);
	}
	
	/**
	 *
	 * @access public
	 * @param Message $message
	 * @return self
	 */
    public function saveDraft($message)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        $folderRepo = $this->container->get('ccdn_message_message.repository.folder');
        $folderManager = $this->container->get('ccdn_message_message.manager.folder');
        $quota = $this->container->getParameter('ccdn_message_message.quotas.max_messages');

        // Get the folders for this user
        $folders = $folderRepo->findAllFoldersForUser($user->getId());

        // Ensure folders exist or create them
        if (! $folders) {
            $folderManager->setupDefaults($recipient->getId())->flush();

            $folders = $folderRepo->findAllFoldersForUser($recipient->getId());
        }

        // Check the used space against the quota
        $used = $folderManager->checkQuotaAllowanceUsed($folders);

        if ($used >= $quota) {
            $this->container->get('session')->setFlash('notice',
                $this->container->get('translator')->trans('ccdn_message_message.flash.message.send.inbox_full', array('%user%' => $user->getUsername()), 'CCDNMessageMessageBundle'));
        } else {
            $this->container->get('session')->setFlash('notice',
                $this->container->get('translator')->trans('ccdn_message_message.flash.message.draft.saved', array('%user%' => $user->getUsername()), 'CCDNMessageMessageBundle'));
        }

        $message->setOwnedBy($user);
        $message->setSentFrom($user);
        $message->setIsDraft(true);
        $message->setIsRead(false);
        $message->setFolder($folders[2]);

        $this->persist($message);
        $this->flush();

        $this->updateAllFolderCachesForUser($user);

        return $this;
    }

    /**
     *
     * @access public
     * @param array $message
     * @return self
     */
    public function sendDraft(array $messages)
    {
        foreach ($messages as $message) {
            $this->insert($message);
        }

        return $this;
    }

    /**
     *
     * @access public
     * @param string $message
	 * @param string $recipient
	 * @param string $sender
	 * @param bool $isCarbonCopy
     * @return \CCDNMessage\MessageBundle\Manager\MessageManager
     */
    protected function receiveMessage(Message $message, $isCarbonCopy = false)
    {
        if ($isCarbonCopy == false && (! is_object($message->getSentTo()) || ! $message->getSentTo() instanceof UserInterface)) {
			throw new \Exception("Sender must be set.");
		}

        $quotaAllowed = $this->getQuotaMaxAllowanceForMessages();
		$recipient = $message->getSentTo();
		$sender = $message->getSentFrom();
		
        $folderManager = $this->managerBag->getFolderManager();
		
		if ($isCarbonCopy) {     
			$folders = $folderManager->findAllFoldersForUserById($sender->getId());
			
            $message->setOwnedBy($sender);
            $message->setFolder($folders[1]);
            $message->setIsRead(true);
		} else {
			$folders = $folderManager->findAllFoldersForUserById($recipient->getId());
			
            $message->setOwnedBy($recipient);
            $message->setFolder($folders[0]);
            $message->setIsRead(false);
		}
        
        // Check quotas.
        $quotaUsed = $folderManager->checkQuotaAllowanceUsed($folders);

        if ($quotaUsed >= $quotaAllowed) {
            //$this->container->get('session')->setFlash('notice',
            //    $this->container->get('translator')->trans('ccdn_message_message.flash.message.send.inbox_full', array('%user%' => $recipient->getUsername()), 'CCDNMessageMessageBundle'));

            return $this;
        } else {
            if (! $isCarbonCopy) {
            //    $this->container->get('session')->setFlash('notice',
            //        $this->container->get('translator')->trans('ccdn_message_message.flash.message.sent.success', array('%user%' => $recipient->getUsername()), 'CCDNMessageMessageBundle'));
            }
        }

        $this->persist($message);

        return $this;
    }

    /**
     *
     * @access public
     * @param string $subject
	 * @param string $messageBody
	 * @param string $recipients
	 * @param \Symfony\Component\Security\Core\User\UserInterface $recipient
	 * @param \Symfony\Component\Security\Core\User\UserInterface $sender
	 * @param \DateTime $createdDate
	 * @param \DateTime $sentDate
     * @return \CCDNMessage\MessageBundle\Manager\MessageManager
     */
	public function createNewMessage($subject, $messageBody, $recipients, UserInterface $recipient = null, UserInterface $sender = null, \DateTime $createdDate = null, \DateTime $sentDate = null)
	{
        $temp = new Message();
        $temp->setSendTo($recipients);
        $temp->setSentTo($recipient);
        $temp->setSentFrom($sender);
        $temp->setSubject($subject);
        $temp->setBody($messageBody);
        $temp->setCreatedDate(($createdDate) ?: new \DateTime('now'));
        $temp->setSentDate(($sentDate) ?: new \DateTime('now'));
        $temp->setIsDraft(true);
        $temp->setIsFlagged(false);
		$temp->setIsRead(false);
        //$temp->setAttachment($message->getAttachment());
		
		return $temp;
	}
	
    /**
     *
     * @access public
     * @param Message $message
     * @return self
     */
    public function sendMessage(Message $message)
	{
		$recipients = $this->getRecipients($message);
		
		$sender = $this->getUser();
		
		// Create a new Thread.
		//$thread = 
		
        foreach ($recipients as $recipientKey => $recipient) {
			$messageCopy = $this->createNewMessage($message->getSubject(), $message->getBody(), $message->getSendTo(), $recipient, $sender, null, null);
	        $messageCopy->setIsDraft(false);
			
            // Send.
		    $this->receiveMessage($messageCopy, false)->flush();

	        // Update recipients folders read/unread cache counts.			
			$this->updateAllFolderCachesForUser($recipient)->flush();
        }

        // Add Carbon Copy.
		$messageCopy = $this->createNewMessage($message->getSubject(), $message->getBody(), $message->getSendTo(), $sender, $sender, null, null);
        $messageCopy->setIsDraft(false);
		
        $this->receiveMessage($message, true)->flush();

		$this->updateAllFolderCachesForUser($sender)->flush();
		
		return $this;
	}
	
    /**
     *
     * @access public
     * @param Message $message
     * @return self
     */
    public function replyToMessage(Message $message, Message $regardingMessage)
	{
		$recipients = $this->getRecipients($message);
		
		$sender = $this->getUser();
		
		// Get existing Thread.
		//$thread = 
		
        foreach ($recipients as $recipientKey => $recipient) {
			$messageCopy = $this->createMessage($message->getSubject(), $message->getBody(), $message->getSendTo(), $recipient, $sender, null, null);
	        $messageCopy->setIsDraft(false);
			
            // Send.
		    $this->receiveMessage($messageCopy, false)->flush();

	        // Update recipients folders read/unread cache counts.			
			$this->updateAllFolderCachesForUser($recipient)->flush();
        }

        // Add Carbon Copy.
		$messageCopy = $this->createMessage($message->getSubject(), $message->getBody(), $message->getSendTo(), $sender, $sender, null, null);
        $messageCopy->setIsDraft(false);
		
        $this->receiveMessage($message, true)->flush();
		
		
		return $this;
	}
	
    /**
     *
     * @access public
     * @param Message $message
     * @return self
     */
    public function forwardMessage(Message $message)
	{
		$recipients = $this->getRecipients($message);
		
		$sender = $this->getUser();
		
		// Get existing Thread.
		//$thread = 
		
        foreach ($recipients as $recipientKey => $recipient) {
			$messageCopy = $this->createMessage($message->getSubject(), $message->getBody(), $message->getSendTo(), $recipient, $sender, null, null);
	        $messageCopy->setIsDraft(false);
			
            // Send.
		    $this->receiveMessage($messageCopy, false)->flush();

	        // Update recipients folders read/unread cache counts.			
			$this->updateAllFolderCachesForUser($recipient)->flush();
        }

        // Add Carbon Copy.
		$messageCopy = $this->createMessage($message->getSubject(), $message->getBody(), $message->getSendTo(), $sender, $sender, null, null);
        $messageCopy->setIsDraft(false);
		
        $this->receiveMessage($message, true)->flush();
		
		
		return $this;
	}
	
    /**
     *
     * @access public
     * @param Message $message
     * @return self
     */
	public function getRecipients(Message $message)
	{
		$recipients = $message->getSendTo();
		
        // build a list of recipients from the sendTo field.
        if ($recipients = preg_split('/((,)|(\s))/', $recipients, PREG_OFFSET_CAPTURE)) {
            foreach ($recipients as $key => $recipient) {
                $recipients[$key] = preg_replace("/[^a-zA-Z0-9_]/", "", $recipients[$key]);

                if (! $recipient) {
                    unset($recipients[$key]);
                }
            }

            $users = $this->managerBag->getUserProvider()->findTheseUsersByUsername($recipients);
        } else {
            $recipients = array($recipients);

            $users = $this->managerBag->getUserProvider()->findOneByUsername($recipients);
        }
		
		return $users;
	}
	
//    /**
//     *
//     * @access public
//     * @param Message $message
//     * @return self
//     */
//    public function insert($message)
//    {
//        $recipients = $message->getSendTo();
//
//        // build a list of recipients from the sendTo field.
//        if ($recipients = preg_split('/((,)|(\s))/', $recipients, PREG_OFFSET_CAPTURE)) {
//            foreach ($recipients as $key => $recipient) {
//                $recipients[$key] = preg_replace("/[^a-zA-Z0-9_]/", "", $recipients[$key]);
//
//                if (! $recipient) {
//                    unset($recipients[$key]);
//                }
//            }
//
//            $sendToUsers = $this->container->get('ccdn_user_user.repository.user')->findTheseUsersByUsername($recipients);
//        } else {
//            $recipients = array($value);
//
//            $sendToUsers = $this->container->get('ccdn_user_user.repository.user')->findByUsername($recipients);
//        }
//
//        $user = $this->container->get('security.context')->getToken()->getUser();
//
//        // Send.
//        foreach ($sendToUsers as $recipient_key => $recipient) {
//            $this->sendTo($message, $recipient, $user, false);
//        }
//
//        // add Carbon Copy.
//        $this->sendTo($message, null, $user, true);
//
//        $this->flush();
//
//        // Update recipients folders read/unread cache counts.
//        foreach ($sendToUsers as $recipient) {
//            $this->updateAllFolderCachesForUser($recipient);
//        }
//
//        return $this;
//    }

    /**
     *
     * @access public
     * @param Message $message
     * @return self
     */
    public function markAsRead($message)
    {
        $message->setIsRead(true);
        $this->persist($message)->flush();

        return $this;
    }

    /**
     *
     * @access public
     * @param Message $message
     * @return self
     */
    public function markAsUnread($message)
    {
        $message->setIsRead(false);
        $this->persist($message)->flush();

        return $this;
    }

    /**
     *
     * @access public
     * @param Message $message, array $folders
     * @return self
     */
    public function delete($message, $folders)
    {

        if ($message->getFolder()->getName() == 'trash') {
            $this->remove($message);
        } else {
            foreach ($folders as $folder) {
                if ($folder->getName() == 'trash') {
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
     * @param array $messages, array $folders
     * @return self
     */
    public function bulkDelete($messages, $folders)
    {
        // find the trash folder
        foreach ($folders as $folder) {
            if ($folder->getName() == 'trash') {
                $trash = $folder;

                break;
            }
        }

        // trash or remove each message
        foreach ($messages as $message) {
            if ($message->getFolder()->getName() == 'trash') {
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
     * @param array $messages
     * @return self
     */
    public function bulkMarkAsRead($messages)
    {
        foreach ($messages as $message) {
            $message->setIsRead(true);
            $this->persist($message);
        }

        return $this;
    }

    /**
     *
     * @access public
     * @param array $messages
     * @return self
     */
    public function bulkMarkAsUnread($messages)
    {
        foreach ($messages as $message) {
            $message->setIsRead(false);
            $this->persist($message);
        }

        return $this;
    }

    /**
     *
     * @access public
     * @param array $messages, Folder $moveTo
     * @return self
     */
    public function bulkMoveToFolder($messages, $moveTo)
    {
        foreach ($messages as $message) {
            $message->setFolder($moveTo);
            $this->persist($message);
        }

        return $this;
    }

    /**
     *
     * @access public
     * @param $user
     * @return self
     */
    public function updateAllFolderCachesForUser($user)
    {
		$folderManager = $this->managerBag->getFolderManager();
		
        $folders = $folderManager->findAllFoldersForUserById($user->getId());

        foreach ($folders as $folder) {
            $folderManager->updateFolderCounterCaches($folder);
        }

        $folderManager->flush();

        $this->managerBag->getRegistryManager()->updateCacheUnreadMessagesForUser($user)->flush();

        return $this;
    }
}