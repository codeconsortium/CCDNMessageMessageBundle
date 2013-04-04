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

namespace CCDNMessage\MessageBundle\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

use CCDNMessage\MessageBundle\Manager\BaseManagerInterface;
use CCDNMessage\MessageBundle\Entity\Message;

/**
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
class MessageForwardFormHandler
{
    /**
	 *
	 * @access protected
	 * @var \Symfony\Component\Form\FormFactory $factory
	 */
    protected $factory;
	
	/**
	 *
	 * @access protected
	 * @var \CCDNMessage\MessageBundle\Form\Type\MessageFormType $messageFormType
	 */
	protected $messageFormType;
	
    /**
	 *
	 * @access protected
	 * @var \CCDNMessage\MessageBundle\Manager\BaseManagerInterface $manager
	 */
    protected $manager;

    /**
	 * 
	 * @access protected
	 * @var \CCDNMessage\MessageBundle\Form\Type\MessageFornType $form 
	 */
    protected $form;

    /**
	 * 
	 * @access protected
	 * @var \Symfony\Component\Security\Core\User\UserInterface $sender 
	 */
    protected $sender;

    /**
	 * 
	 * @access protected
	 * @var \Symfony\Component\Security\Core\User\UserInterface $recipient 
	 */
    protected $recipient;
	
    /**
	 * 
	 * @access protected
	 * @var \CCDNMessage\MessageBundle\Entity\Message $forwardingMessage 
	 */
	protected $forwardingMessage;
	
    /**
     *
     * @access public
     * @param \Symfony\Component\Form\FormFactory $factory
	 * @param \CCDNMessage\MessageBundle\Form\Type\MessageFormType $messageFormType
	 * @param \CCDNMessage\MessageBundle\Manager\BaseManagerInterface $manager
     */
    public function __construct(FormFactory $factory, $messageFormType, BaseManagerInterface $manager)
    {
        $this->factory = $factory;
		$this->messageFormType = $messageFormType;
		
        $this->manager = $manager;
    }

	/**
	 *
	 * @access public
	 * @param \Symfony\Component\Security\Core\User\UserInterface $sender
	 * @return \CCDNMessage\MessageBundle\Form\Handler\MessageFormHandler
	 */
	public function setSender(UserInterface $sender)
	{
		$this->sender = $sender;
		
		return $this;
	}

	/**
	 *
	 * @access public
	 * @param \Symfony\Component\Security\Core\User\UserInterface $sender
	 * @return \CCDNMessage\MessageBundle\Form\Handler\MessageFormHandler
	 */	
	public function setRecipient(UserInterface $recipient)
	{
		$this->recipient = $recipient;
		
		return $this;
	}
	
	/**
	 *
	 * @access public
	 * @param \CCDNMessage\MessageBundle\Entity\Message $forwardingMessage
	 * @return \CCDNMessage\MessageBundle\Form\Handler\MessageFormHandler
	 */	
	public function setMessageToForward(Message $forwardingMessage)
	{
		$this->forwardingMessage = $forwardingMessage;
		
		return $this;
	}
	
	/**
	 *
	 * @access public
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @return string
	 */
	public function getSubmitAction(Request $request)
	{
		if ($request->request->has('submit')) {
			$action = key($request->request->get('submit'));
		} else {
			$action = 'post';
		}
		
		return $action;
	}
	
    /**
     *
     * @access public
	 * @param \Symfony\Component\HttpFoundation\Request $request
     * @return bool
     */
    public function process(Request $request)
    {
        $this->getForm();

        if ($request->getMethod() == 'POST') {
			$this->form->bindRequest($request);

            if ($this->form->isValid()) {
	            $message = $this->form->getData();

		        $message->setSentFromUser($this->sender);
				$message->setCreatedDate(new \DateTime());
				
				$isFlagged = $this->form->get('is_flagged')->getData();
				
				if ($this->getSubmitAction($request) == 'save_draft') {
					$this->manager->saveDraft($message, $isFlagged)->flush();
					
					return false;
				}

				if ($this->getSubmitAction($request) == 'send') {
	                $this->onSuccess($message, $isFlagged);

	                return true;					
				}
            }
        }

        return false;
    }

    /**
     *
     * @access public
     * @return Form
     */
    public function getForm()
    {
        if (null == $this->form) {
            $defaultValues = array();
            
			if (is_object($this->forwardingMessage) && $this->forwardingMessage instanceof Message) {
				if (is_object($this->recipient) && $this->recipient instanceof UserInterface) {
					$defaultValues['send_to'] = $this->recipient->getUsername();
				} else {
					if ($this->forwardingMessage->getSentFromUser()) {
		                $defaultValues['send_to'] = $this->forwardingMessage->getSentFromUser()->getUsername();					
					}
				}
				
				$defaultValues['subject'] = $this->forwardingMessage->getSubject();
				$defaultValues['body'] = $this->forwardingMessage->getBody();
            } else {
				if (is_object($this->recipient) && $this->recipient instanceof UserInterface) {
					$defaultValues['send_to'] = $this->recipient->getUsername();
				}
            }

			$this->form = $this->factory->create($this->messageFormType, null, $defaultValues);
        }

        return $this->form;
    }

    /**
     *
     * @access protected
     * @param \CCDNMessage\MessageBundle\Entity\Message $message
     * @return MessageManager
     */
    protected function onSuccess(Message $message, $isFlagged)
    {
		return $this->manager->sendForwardMessage($message, $this->forwardingMessage, $isFlagged)->flush();
    }
}