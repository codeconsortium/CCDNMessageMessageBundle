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

use CCDNMessage\MessageBundle\Model\Manager\ManagerInterface;
use CCDNMessage\MessageBundle\Model\Manager\BaseManager;

use CCDNMessage\MessageBundle\Entity\Envelope;
use CCDNMessage\MessageBundle\Entity\Message;
use CCDNMessage\MessageBundle\Entity\Thread;

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
class MessageManager extends BaseManager implements ManagerInterface
{
    /**
     *
     * @access public
     * @param  array                                                   $envelopes
     * @return \CCDNMessage\MessageBundle\Model\Manager\MessageManager
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
     * @param  \CCDNMessage\MessageBundle\Entity\Envelope              $envelope
     * @return \CCDNMessage\MessageBundle\Model\Manager\MessageManager
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
     * @param  \CCDNMessage\MessageBundle\Entity\Message               $message
     * @param  bool                                                    $isFlagged
     * @return \CCDNMessage\MessageBundle\Model\Manager\MessageManager
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
     * @param  \CCDNMessage\MessageBundle\Entity\Message               $message
     * @param  bool                                                    $isFlagged
     * @return \CCDNMessage\MessageBundle\Model\Manager\MessageManager
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
     * @param  \CCDNMessage\MessageBundle\Entity\Message               $message
     * @param  \CCDNMessage\MessageBundle\Entity\Message               $regardingMessage
     * @param  bool                                                    $isFlagged
     * @return \CCDNMessage\MessageBundle\Model\Manager\MessageManager
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
     * @param  \CCDNMessage\MessageBundle\Entity\Message               $message
     * @param  \CCDNMessage\MessageBundle\Entity\Message               $forwardingMessage
     * @param  bool                                                    $isFlagged
     * @return \CCDNMessage\MessageBundle\Model\Manager\MessageManager
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
     * @param  \CCDNMessage\MessageBundle\Entity\Message               $message
     * @return \CCDNMessage\MessageBundle\Model\Manager\MessageManager
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
