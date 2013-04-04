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

namespace CCDNMessage\MessageBundle\Controller;

use CCDNMessage\MessageBundle\Controller\BaseController;

use CCDNMessage\MessageBundle\Entity\Message;
use CCDNMessage\MessageBundle\Entity\Envelope;

/**
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
class MessageBaseController extends BaseController
{
	/**
	 *
	 * @access protected
	 * @param int $userId
	 * @return \CCDNMessage\MessageBundle\Form\Handler\MessageFormHandler
	 */
	protected function getFormHandlerToSendMessage($userId = null)
	{
        $formHandler = $this->container->get('ccdn_message_message.form.handler.message');
			
		$formHandler->setSender($this->getUser());	

        // Are we sending this to someone who's 'send message' button we clicked?
        if (null != $userId) {
            $sendToUser = $this->getUserProvider()->findOneUserById($userId);

            $formHandler->setRecipient($sendToUser);
        }
		
		return $formHandler;
	}
	
	/**
	 *
	 * @access protected
	 * @param int $userId
	 * @param \CCDNMessage\MessageBundle\Entity\Envelope
	 * @return \CCDNMessage\MessageBundle\Form\Handler\MessageReplyFormHandler
	 */
	protected function getFormHandlerToReplyToMessage($userId = null, Envelope $inResponseEnvelope)
	{
        $formHandler = $this->container->get('ccdn_message_message.form.handler.message_reply');
			
		$formHandler->setSender($this->getUser());

        // Are we sending this to someone who's 'send message' button we clicked?
        if (null != $userId) {
            $sendToUser = $this->getUserProvider()->findOneUserById($userId);

            $formHandler->setRecipient($sendToUser);
        }
		
		$formHandler->setInResponseToMessage($inResponseEnvelope->getMessage());
		
		return $formHandler;
	}
	
	/**
	 *
	 * @access protected
	 * @param int $userId
	 * @param \CCDNMessage\MessageBundle\Entity\Envelope
	 * @return \CCDNMessage\MessageBundle\Form\Handler\MessageForwardFormHandler
	 */
	protected function getFormHandlerToForwardMessage($userId = null, Envelope $envelopeToForward)
	{
        $formHandler = $this->container->get('ccdn_message_message.form.handler.message_forward');
			
		$formHandler->setSender($this->getUser());

        // Are we sending this to someone who's 'send message' button we clicked?
        if (null != $userId) {
            $sendToUser = $this->getUserProvider()->findOneUserById($userId);

            $formHandler->setRecipient($sendToUser);
        }
		
		$formHandler->setMessageToForward($envelopeToForward->getMessage());
		
		return $formHandler;
	}
}