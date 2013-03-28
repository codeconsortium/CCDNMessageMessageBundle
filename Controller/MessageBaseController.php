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

/**
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
class MessageBaseController extends BaseController
{
	protected function getFormHandlerToSendMessage($userId = null, $inResponseMessageId = null, $forwardMessageId = null)
	{
        $formHandler = $this->container->get('ccdn_message_message.form.handler.message');
			
		$formHandler->setSender($this->getUser());	

        // Are we sending this to someone who's 'send message' button we clicked?
		/** 
		 * @todo add a user provider
		 */
        if (null != $userId) {
            $sendToUser = $this->container->get('ccdn_user_user.repository.user')->findOneById($userId);

            $formHandler->setRecipient($sendToUser);
        }
		
		if (null != $inResponseMessageId) {
			$inResponseMessage = $this->getMessageManager()->findMessageByIdForUser($inResponseMessageId, $this->getUser()->getId());
			$this->isFound($inResponseMessage, 'Message could not be found.');
			
			$formHandler->setInResponseToMessage($inResponseMessage);
		}

		if (null != $forwardMessageId) {
			$forwardMessage = $this->getMessageManager()->findMessageByIdForUser($forwardMessageId, $this->getUser()->getId());		
	        $this->isFound($forwardMessage, 'Message could not be found.');
			
			$formHandler->setMessageToForward($forwardMessage);
		}
		
		return $formHandler;
	}
}