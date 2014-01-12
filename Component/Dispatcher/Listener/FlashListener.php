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

namespace CCDNMessage\MessageBundle\Component\Dispatcher\Listener;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;

use CCDNMessage\MessageBundle\Component\Dispatcher\MessageEvents;
use CCDNMessage\MessageBundle\Component\Dispatcher\Event\UserMessageFloodEvent;
use CCDNMessage\MessageBundle\Component\Dispatcher\Event\UserEnvelopeReceiveEvent;
use CCDNMessage\MessageBundle\Component\Dispatcher\Event\UserEnvelopeReceiveFailedInboxFullEvent;
use CCDNMessage\MessageBundle\Component\Dispatcher\Event\UserEnvelopeReceiveFailedOutboxFullEvent;
use CCDNMessage\MessageBundle\Component\Dispatcher\Event\UserMessageResponseEvent;

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
class FlashListener implements EventSubscriberInterface
{
    /**
     *
     * @access private
     * @var \Symfony\Component\HttpFoundation\Session\Session $session
     */
    protected $session;

    /**
     *
     * @access public
     * @param \Symfony\Component\HttpFoundation\Session\Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     *
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            MessageEvents::USER_MESSAGE_CREATE_FLOODED              => 'onMessageCreateFlooded',
            MessageEvents::USER_MESSAGE_CREATE_REPLY_FLOODED        => 'onMessageCreateFlooded',
            MessageEvents::USER_MESSAGE_CREATE_FORWARD_FLOODED      => 'onMessageCreateFlooded',
            MessageEvents::USER_ENVELOPE_RECEIVE_COMPLETE           => 'onEnvelopeReceiveComplete',
            MessageEvents::USER_ENVELOPE_RECEIVE_FAILED_INBOX_FULL  => 'onEnvelopeReceiveFailedInboxFull',
            MessageEvents::USER_ENVELOPE_RECEIVE_FAILED_OUTBOX_FULL => 'onEnvelopeReceiveFailedOutboxFull',
            MessageEvents::USER_MESSAGE_MARK_AS_READ_RESPONSE       => 'onMessageMarkAsRead',
            MessageEvents::USER_MESSAGE_MARK_AS_UNREAD_RESPONSE     => 'onMessageMarkAsUnread',
            MessageEvents::USER_MESSAGE_DELETE_RESPONSE             => 'onMessageDelete',
        );
    }

    public function onMessageCreateFlooded(UserMessageFloodEvent $event)
    {
        if ($message = $event->getMessage()) {
            if ($message->getId()) {
                $this->session->getFlashBag()->add('success', 'You have sent a lot in a short time, wait a while before trying again.');
            }
        }
    }

    public function onEnvelopeReceiveComplete(UserEnvelopeReceiveEvent $event)
    {
        if ($envelope = $event->getEnvelope()) {
            if ($envelope->getId()) {
                $this->session->getFlashBag()->add('success', 'Message sent successfully to ' . $event->getRecipient()->getUsername() . '.');
            }
        }
    }

    public function onEnvelopeReceiveFailedInboxFull(UserEnvelopeReceiveFailedInboxFullEvent $event)
    {
        if ($event->getEnvelope()) {
            $this->session->getFlashBag()->add('success', 'Message(s) could not be sent because your outbox is full.');
        }
    }

    public function onEnvelopeReceiveFailedOutboxFull(UserEnvelopeReceiveFailedOutboxFullEvent $event)
    {
        if ($event->getEnvelope()) {
            $this->session->getFlashBag()->add('success', 'Message could not be delivered to  "' . $event->getRecipient()->getUsername() . '" because their inbox is full.');
        }
    }

    public function onMessageMarkAsRead(UserMessageResponseEvent $event)
    {
        if ($event->getMessage()) {
            $this->session->getFlashBag()->add('success', 'Marked message "' . $event->getMessage()->getSubject() . '" as read.');
        }
    }

    public function onMessageMarkAsUnread(UserMessageResponseEvent $event)
    {
        if ($event->getMessage()) {
            $this->session->getFlashBag()->add('success', 'Marked message "' . $event->getMessage()->getSubject() . '"  as unread.');
        }
    }

    public function onMessageDelete(UserMessageResponseEvent $event)
    {
        if ($event->getMessage()) {
            $this->session->getFlashBag()->add('success', 'Deleted message "' . $event->getMessage()->getSubject() . '".');
        }
    }
}
