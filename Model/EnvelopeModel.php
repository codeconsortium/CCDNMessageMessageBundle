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

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;

use CCDNMessage\MessageBundle\Entity\Message;
use CCDNMessage\MessageBundle\Entity\Thread;
use CCDNMessage\MessageBundle\Entity\Folder;

abstract class EnvelopeModel
{
	/**
	 * @var \CCDNMessage\MessageBundle\Entity\Folder $folder
	 */
    protected $folder = null;
	
	/**
	 * @var \CCDNMessage\MessageBundle\Entity\Message $message
	 */
    protected $message = null;
	
	/** 
	 * @var \Symfony\Component\Security\Core\User\UserInterface $owmedByUser
	 */
    protected $ownedByUser = null;

	/**
	 * @var \CCDNMessage\MessageBundle\Entity\Thread $thread
	 */
    protected $thread = null;
	
	/**
	 *
	 * @access public
	 */
    public function __construct()
    {
        // your own logic
    }

    /**
     * Get folder
     *
     * @return \CCDNMessage\MessageBundle\Entity\Folder
     */
    public function getFolder()
    {
        return $this->folder;
    }
		
    /**
     * Set folder
     *
     * @param \CCDNMessage\MessageBundle\Entity\Folder $folder
	 * @return \CCDNMessage\MessageBundle\Entity\Envelope
     */
    public function setFolder(Folder $folder = null)
    {
        $this->folder = $folder;
		
		return $this;
    }
	
    /**
     * Get message
     *
     * @return \CCDNMessage\MessageBundle\Entity\Message
     */
	public function getMessage()
	{
		return $this->message;
	}
	
    /**
     * Set message
     *
     * @param \CCDNMessage\MessageBundle\Entity\Message $message
	 * @return \CCDNMessage\MessageBundle\Entity\Envelope
     */
	public function setMessage(Message $message)
	{
		$this->message = $message;
		
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
     * @param \CCDNMessage\MessageBundle\Entity\Thread $thread
	 * @return \CCDNMessage\MessageBundle\Entity\Message
     */
	public function setThread(Thread $thread)
	{
		$this->thread = $thread;
		
		return $this;
	}

    /**
     * Get ownedByUser
     *
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    public function getOwnedByUser()
    {
        return $this->ownedByUser;
    }
	
    /**
     * Set ownedByUser
     *
     * @param \Symfony\Component\Security\Core\User\UserInterface $ownedByUser
	 * @return \CCDNMessage\MessageBundle\Entity\Envelope
     */
    public function setOwnedByUser(UserInterface $ownedByUser = null)
    {
        $this->ownedByUser = $ownedByUser;
		
		return $this;
    }
}