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

namespace CCDNMessage\MessageBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

use CCDNMessage\MessageBundle\Entity\Message;

abstract class ThreadModel
{
    /**
	 * @var \Doctrine\Common\Collections\ArrayCollection $messages
	 */
    protected $messages = null;
	
	/**
	 *
	 * @access public
	 */
    public function __construct()
    {
        // your own logic
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getMessages()
    {
        return $this->messages;
    }
	
    /**
     * Set messages
     *
     * @param \Doctrine\Common\Collections\ArrayCollection|Array $messages
	 * @return \CCDNMessage\MessageBundle\Entity\Message
     */
    public function setMessages($messages = null)
    {
        $this->messages = $messages;
		
		return $this;
    }
	
    /**
     * Add message
     *
	 * @param \CCDNMessage\MessageBundle\Entity\Message $message
     * @return \CCDNMessage\MessageBundle\Entity\Message
     */
    public function addMessage(Message $message)
    {
        $this->messages[] = $message;
		
		return $this;
    }
}