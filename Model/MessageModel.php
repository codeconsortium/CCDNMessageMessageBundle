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

use CCDNMessage\MessageBundle\Entity\Folder;	
use CCDNMessage\MessageBundle\Entity\Thread;
use CCDNMessage\MessageBundle\Entity\Message;

abstract class MessageModel
{
	/**
	 * @var \CCDNMessage\MessageBundle\Entity\Folder $folder
	 */
    protected $folder = null;

	/**
	 * @var \Symfony\Component\Security\Core\User\UserInterface $sentTo
	 */
    protected $sentTo = null;
	
	/**
	 * @var \Symfony\Component\Security\Core\User\UserInterface $sentFrom
	 */
    protected $sentFrom = null;
	
	/** 
	 * @var \Symfony\Component\Security\Core\User\UserInterface $owmedBy
	 */
    protected $ownedBy = null;

	/**
	 * @var \CCDNMessage\MessageBundle\Entity\Message $inResponseTo
	 */
    protected $inResponseTo = null;
	
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
	 * @return \CCDNMessage\MessageBundle\Entity\Message
     */
    public function setFolder(Folder $folder = null)
    {
        $this->folder = $folder;
		
		return $this;
    }

    /**
     * Get sentTo
     *
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    public function getSentTo()
    {
        return $this->sentTo;
    }
	
    /**
     * Set sentTo
     *
     * @param \Symfony\Component\Security\Core\User\UserInterface $sentTo
	 * @return \CCDNMessage\MessageBundle\Entity\Message
     */
    public function setSentTo(UserInterface $sentTo = null)
    {
        $this->sentTo = $sentTo;
		
		return $this;
    }

    /**
     * Get sentFrom
     *
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    public function getSentFrom()
    {
        return $this->sentFrom;
    }

    /**
     * Set sentFrom
     *
     * @param \Symfony\Component\Security\Core\User\UserInterface $sentFrom
	 * @return \CCDNMessage\MessageBundle\Entity\Message
     */
    public function setSentFrom(UserInterface $sentFrom = null)
    {
        $this->sentFrom = $sentFrom;
		
		return $this;
    }

    /**
     * Get ownedBy
     *
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    public function getOwnedBy()
    {
        return $this->ownedBy;
    }
	
    /**
     * Set ownedBy
     *
     * @param \Symfony\Component\Security\Core\User\UserInterface $ownedBy
	 * @return \CCDNMessage\MessageBundle\Entity\Message
     */
    public function setOwnedBy(UserInterface $ownedBy = null)
    {
        $this->ownedBy = $ownedBy;
		
		return $this;
    }
	
    /**
     * Get inResponseTo
     *
     * @return \CCDNMessage\MessageBundle\Entity\Message
     */
	public function getInResponseTo()
	{
		return $this->inResponseTo;
	}
	
    /**
     * Set inResponseTo
     *
     * @param \CCDNMessage\MessageBundle\Entity\Message $inResponseTo
	 * @return \CCDNMessage\MessageBundle\Entity\Message
     */
	public function setInResponseTo(Message $inResponseTo)
	{
		$this->inResponseTo = $inResponseTo;
		
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
}