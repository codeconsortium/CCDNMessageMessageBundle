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

namespace CCDNMessage\MessageBundle\Entity\Model;

use Symfony\Component\Security\Core\User\UserInterface;
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
 * @abstract
 *
 */
abstract class MessageModel
{
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection $envelopes
     */
    protected $envelopes = null;

    /**
     * @var \CCDNMessage\MessageBundle\Entity\Thread $thread
     */
    protected $thread = null;

    /**
     * @var \Symfony\Component\Security\Core\User\UserInterface $sentFromUser
     */
    protected $sentFromUser = null;

    /**
     *
     * @access public
     */
    public function __construct()
    {
        // your own logic
    }

    /**
     * Get envelopes
     *
     * @return array
     */
    public function getEnvelopes()
    {
        return $this->envelopes;
    }

    /**
     * Set envelopes
     *
     * @param $envelopes
     * @return \CCDNMessage\MessageBundle\Entity\Message
     */
    public function setEnvelopes($envelopes = null)
    {
        $this->envelopes = $envelopes;

        return $this;
    }

    /**
     * Get thread
     *
     * @return \CCDNMessage\MessageBundle\Entity\Thread
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * Set thread
     *
     * @param  \CCDNMessage\MessageBundle\Entity\Thread  $thread
     * @return \CCDNMessage\MessageBundle\Entity\Message
     */
    public function setThread(Thread $thread)
    {
        $this->thread = $thread;

        return $this;
    }

    /**
     * Get sentFromUser
     *
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    public function getSentFromUser()
    {
        return $this->sentFromUser;
    }

    /**
     * Set sentFromUser
     *
     * @param  \Symfony\Component\Security\Core\User\UserInterface $sentFromUser
     * @return \CCDNMessage\MessageBundle\Entity\Envelope
     */
    public function setSentFromUser(UserInterface $sentFromUser = null)
    {
        $this->sentFromUser = $sentFromUser;

        return $this;
    }
}
