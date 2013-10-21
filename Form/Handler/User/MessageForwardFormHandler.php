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

namespace CCDNMessage\MessageBundle\Form\Handler\User;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpKernel\Debug\ContainerAwareTraceableEventDispatcher;

use CCDNMessage\MessageBundle\Form\Handler\BaseFormHandler;
use CCDNMessage\MessageBundle\Model\Model\ModelInterface;
use CCDNMessage\MessageBundle\Entity\Message;

use CCDNMessage\MessageBundle\Component\Server\MessageServer;
use CCDNMessage\MessageBundle\Component\FloodControl;
use CCDNMessage\MessageBundle\Component\Dispatcher\MessageEvents;
use CCDNMessage\MessageBundle\Component\Dispatcher\Event\UserMessageFloodEvent;
use CCDNMessage\MessageBundle\Component\Dispatcher\Event\UserMessageEvent;

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
class MessageForwardFormHandler extends BaseFormHandler
{
    /**
     *
     * @access protected
     * @var \CCDNMessage\MessageBundle\Form\Type\MessageFormType $messageFormType
     */
    protected $messageFormType;

    /**
     *
     * @access protected
     * @var \CCDNMessage\MessageBundle\Model\Model\ModelInterface $model
     */
    protected $model;

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
     * @access protected
     * @var \CCDNMessage\MessageBundle\Component\FloodControl $floodControl
     */
    protected $floodControl;

    /**
     *
     * @access protected
     * @var \CCDNMessage\MessageBundle\Component\Server\MessageServer $messageServer
     */
    protected $messageServer;

    /**
     *
     * @access public
     * @param Symfony\Component\HttpKernel\Debug\ContainerAwareTraceableEventDispatcher $dispatcher
     * @param \Symfony\Component\Form\FormFactory                                       $factory
     * @param \CCDNMessage\MessageBundle\Form\Type\MessageFormType                      $messageFormType
     * @param \CCDNMessage\MessageBundle\Model\Model\ModelInterface                     $model
     * @param |CCDNMessage\MessageBundle\Component\FloodControl                         $floodControl
     * @param \CCDNMessage\MessageBundle\Component\Server\MessageServer                  $messageServer
     */
    public function __construct(ContainerAwareTraceableEventDispatcher $dispatcher, FormFactory $factory, $messageFormType, ModelInterface $model, FloodControl $floodControl, MessageServer $messageServer)
    {
		$this->dispatcher = $dispatcher;
        $this->factory = $factory;
        $this->messageFormType = $messageFormType;
        $this->model = $model;
		$this->floodControl = $floodControl;
		$this->messageServer = $messageServer;
    }

    /**
     *
     * @access public
     * @param  \Symfony\Component\Security\Core\User\UserInterface        $sender
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
     * @param  \Symfony\Component\Security\Core\User\UserInterface        $sender
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
     * @param  \CCDNMessage\MessageBundle\Entity\Message                  $forwardingMessage
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
     * @return bool
     */
    public function process()
    {
        $this->getForm();
        
		if ($this->floodControl->isFlooded()) {
            $this->dispatcher->dispatch(MessageEvents::USER_MESSAGE_CREATE_FORWARD_FLOODED, new UserMessageFloodEvent($this->request));

            return false;
        }

        $this->floodControl->incrementCounter();
		
        if ($this->request->getMethod() == 'POST') {
            $this->form->bindRequest($this->request);

            if ($this->form->isValid()) {
                $message = $this->form->getData();
                $message->setSentFromUser($this->sender);
                $message->setCreatedDate(new \DateTime());
                $isFlagged = $this->form->get('is_flagged')->getData();

                if ($this->getSubmitAction($this->request) == 'save_draft') {
                    $this->model->saveDraft($message, $isFlagged)->flush();

                    return false;
                }

                if ($this->getSubmitAction($this->request) == 'send') {
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
     * @param  \CCDNMessage\MessageBundle\Entity\Message             $message
     * @return \CCDNMessage\MessageBundle\Model\Model\ModelInterface
     */
    protected function onSuccess(Message $message, $isFlagged)
    {
        $this->dispatcher->dispatch(MessageEvents::USER_MESSAGE_CREATE_FORWARD_SUCCESS, new UserMessageEvent($this->request, $message));
		
		$this->messageServer->sendMessage($message, $isFlagged);
		
        $this->dispatcher->dispatch(MessageEvents::USER_MESSAGE_CREATE_FORWARD_COMPLETE, new UserMessageEvent($this->request, $message));
		
		return $this->model;
    }
}
