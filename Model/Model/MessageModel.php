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
	 * @param  \CCDNMessage\MessageBundle\Entity\Message               $message
	 * @return \CCDNMessage\MessageBundle\Model\Manager\MessageManager
	 */
	public function saveMessage(Message $message)
	{
		return $this->getManager()->saveMessage($message);
	}
}