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

use CCDNMessage\MessageBundle\Entity\Envelope;
use CCDNMessage\MessageBundle\Entity\Message;
use CCDNMessage\MessageBundle\Entity\Thread;

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
	 * @param int $messageId
	 * @return \CCDNMessage\MessageBundle\Entity\Message
	 */	
	public function getAllEnvelopesForMessageById($messageId)
	{
		if (null == $messageId || ! is_numeric($messageId) || $messageId == 0) {
			throw new \Exception('Message id "' . $messageId . '" is invalid!');
		}
		
		$params = array(':messageId' => $messageId);
		
		$qb = $this->createSelectQuery(array('m', 'm_e', 'm_t', 'm_t_messages'));
		
		$qb
			->leftJoin('m.envelopes', 'm_e')
			->leftJoin('m.thread', 'm_t')
			->leftJoin('m_t.messages', 'm_t_messages')
			->where('m.id = :messageId')
			->setParameters($params)
			->addOrderBy('m.createdDate', 'DESC')
		;
		
		return $this->gateway->findMessage($qb, $params);
	}

    /**
     *
     * @access public
     * @param array $envelopes
     * @return \CCDNMessage\MessageBundle\Manager\MessageManager
     */
    public function bulkSendDraft(array $envelopes)
    {
        foreach ($envelopes as $envelope) {
            $this->sendDraft($envelope);
        }

		$this->flush();
		
        return $this;
    }

    /**
     *
     * @access public
     * @param \CCDNMessage\MessageBundle\Entity\Envelope $envelope
     * @return \CCDNMessage\MessageBundle\Manager\MessageManager
     */
    public function sendDraft(Envelope $envelope)
    {
		$isFlagged = $envelope->getIsFlagged();
		$message = $envelope->getMessage();
		
		$recipients = $this->getRecipients($message);
		
		$sender = $this->getUser();
		
		// Create a new Thread?
		if ($message->getThread()) {
			$thread = $message->getThread();			
		} else {
			$thread = new Thread();
		
			$message->setThread($thread);
			$this->persist($message)->flush()->refresh($message)->refresh($thread);
		}
		
		$envelopeManager = $this->managerBag->getEnvelopeManager();
		
        foreach ($recipients as $recipientKey => $recipient) {		
            // Send.
		    $envelopeManager->receiveMessage($message, $thread, $recipient, $envelopeManager::MESSAGE_SEND, $isFlagged);
        }
		
		return $this;
    }

    /**
     *
     * @access public
     * @param \CCDNMessage\MessageBundle\Entity\Message $message
	 * @param bool $isFlagged
     * @return \CCDNMessage\MessageBundle\Manager\MessageManager
     */
    public function saveDraft(Message $message, $isFlagged)
	{		
		$sender = $this->getUser();
		
		// Create a new Thread?
		if ($message->getThread()) {
			$thread = $message->getThread();			
		} else {
			$thread = new Thread();
		}
		
		$message->setThread($thread);
		$this->persist($message)->flush()->refresh($message);
		
		$envelopeManager = $this->managerBag->getEnvelopeManager();
		
        // Save draft.	
        $envelopeManager->receiveMessage($message, $thread, $sender, $envelopeManager::MESSAGE_SAVE_DRAFT, $isFlagged);
		
		return $this;
	}
		
    /**
     *
     * @access public
     * @param \CCDNMessage\MessageBundle\Entity\Message $message
	 * @param bool $isFlagged
     * @return \CCDNMessage\MessageBundle\Manager\MessageManager
     */
    public function sendMessage(Message $message, $isFlagged)
	{
		$recipients = $this->getRecipients($message);
		
		$sender = $this->getUser();
		
		// Create a new Thread.
		$thread = new Thread();
		$message->setThread($thread);
		$this->persist($message)->flush()->refresh($message);
		
		$envelopeManager = $this->managerBag->getEnvelopeManager();
		
        foreach ($recipients as $recipientKey => $recipient) {		
            // Send.
		    $envelopeManager->receiveMessage($message, $thread, $recipient, $envelopeManager::MESSAGE_SEND, $isFlagged);
        }

        // Add Carbon Copy.	
        $envelopeManager->receiveMessage($message, $thread, $sender, $envelopeManager::MESSAGE_SAVE_CARBON_COPY, $isFlagged);
		
		return $this;
	}
	
    /**
     *
     * @access public
     * @param \CCDNMessage\MessageBundle\Entity\Message $message
     * @param \CCDNMessage\MessageBundle\Entity\Message $regardingMessage
	 * @param bool $isFlagged
     * @return \CCDNMessage\MessageBundle\Manager\MessageManager
     */
    public function sendReplyToMessage(Message $message, Message $regardingMessage, $isFlagged)
	{
		$recipients = $this->getRecipients($message);
		
		$sender = $this->getUser();
		
		// Set Thread.
		$thread = $regardingMessage->getThread();
		$message->setThread($thread);
		$this->persist($message)->flush()->refresh($message);
		
		$envelopeManager = $this->managerBag->getEnvelopeManager();
		
        foreach ($recipients as $recipientKey => $recipient) {		
            // Send.
		    $envelopeManager->receiveMessage($message, $thread, $recipient, $envelopeManager::MESSAGE_SEND, $isFlagged);
        }

        // Add Carbon Copy.	
        $envelopeManager->receiveMessage($message, $thread, $sender, $envelopeManager::MESSAGE_SAVE_CARBON_COPY, $isFlagged);
		
		return $this;
	}
	
    /**
     *
     * @access public
     * @param \CCDNMessage\MessageBundle\Entity\Message $message
     * @param \CCDNMessage\MessageBundle\Entity\Message $forwardingMessage
	 * @param bool $isFlagged
     * @return \CCDNMessage\MessageBundle\Manager\MessageManager
     */
    public function sendForwardMessage(Message $message, Message $forwardingMessage, $isFlagged)
	{
		$recipients = $this->getRecipients($message);
		
		$sender = $this->getUser();
		
		// Set Thread.
		$thread = $forwardingMessage->getThread();
		$message->setThread($thread);
		$this->persist($message)->flush()->refresh($message);
		
		$envelopeManager = $this->managerBag->getEnvelopeManager();
		
        foreach ($recipients as $recipientKey => $recipient) {		
            // Send.
		    $envelopeManager->receiveMessage($message, $thread, $recipient, $envelopeManager::MESSAGE_SEND, $isFlagged);
        }

        // Add Carbon Copy.	
        $envelopeManager->receiveMessage($message, $thread, $sender, $envelopeManager::MESSAGE_SAVE_CARBON_COPY, $isFlagged);
		
		return $this;
	}
	
    /**
     *
     * @access public
     * @param \CCDNMessage\MessageBundle\Entity\Message $message
     * @return \CCDNMessage\MessageBundle\Manager\MessageManager
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

            $users = $this->managerBag->getUserManager()->findTheseUsersByUsername($recipients);
        } else {
            $recipients = array($recipients);

            $users = $this->managerBag->getUserManager()->findOneUserByUsername($recipients);
        }
		
		return $users;
	}
}