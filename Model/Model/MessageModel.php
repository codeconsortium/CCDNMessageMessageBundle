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

namespace CCDNMessage\MessageBundle\Model\Model;

use CCDNMessage\MessageBundle\Model\Model\BaseModel;
use CCDNMessage\MessageBundle\Model\Model\ModelInterface;

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
class MessageModel extends BaseModel implements ModelInterface
{
    /**
     *
     * @access public
     * @param  int                                       $messageId
     * @return \CCDNMessage\MessageBundle\Entity\Message
     */
    public function getAllEnvelopesForMessageById($messageId)
    {
		return $this->getRepository()->getAllEnvelopesForMessageById($messageId);
    }
	
	
    /**
     *
     * @access public
     * @param  array                                                   $envelopes
     * @return \CCDNMessage\MessageBundle\Model\Manager\MessageManager
     */
    public function bulkSendDraft(array $envelopes)
    {
        return $this->getManager()->bulkSendDraft($envelopes);
    }

    /**
     *
     * @access public
     * @param  \CCDNMessage\MessageBundle\Entity\Envelope              $envelope
     * @return \CCDNMessage\MessageBundle\Model\Manager\MessageManager
     */
    public function sendDraft(Envelope $envelope)
    {
        return $this->getManager()->sendDraft($envelope);
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
        return $this->getManager()->saveDraft($message, $isFlagged);
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
        return $this->getManager()->sendMessage($message, $isFlagged);
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
        return $this->getManager()->sendReplyToMessage($message, $regardingMessage, $isFlagged);
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
        return $this->getManager()->sendForwardMessage($message, $forwardingMessage, $isFlagged);
    }

    /**
     *
     * @access public
     * @param  \CCDNMessage\MessageBundle\Entity\Message               $message
     * @return \CCDNMessage\MessageBundle\Model\Manager\MessageManager
     */
    public function getRecipients(Message $message)
    {
        return $this->getManager()->getRecipients($message);
    }
}