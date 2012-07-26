<?php

/*
 * This file is part of the CCDN MessageBundle
 *
 * (c) CCDN (c) CodeConsortium <http://www.codeconsortium.com/> 
 * 
 * Available on github <http://www.github.com/codeconsortium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CCDNMessage\MessageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

use CCDNUser\UserBundle\Entity\User;
//use CCDNMessage\MessageBundle\Form\Validator\Constraints as CCDNMessageAssert;
//	 * @CCDNMessageAssert\SendTo()

/**
 * @ORM\Entity(repositoryClass="CCDNMessage\MessageBundle\Repository\MessageRepository")
 * @ORM\Table(name="CC_Message_Message")
 */
class Message
{
	/**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
	protected $id;
	
	/**
     * @ORM\Column(type="string")
     */
    protected $subject;
	
	/**
     * @ORM\Column(type="text")
     */
	protected $body;

	/**
     * @ORM\ManyToOne(targetEntity="CCDNMessage\MessageBundle\Entity\Folder", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_folder_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $folder = null;

	/**
	 * @ORM\Column(type="datetime", name="sent_date", nullable=true)
	 */
	protected $sentDate;
	
	/**
	 * @ORM\Column(type="datetime", name="created_date", nullable=true)
	 */
	protected $createdDate;
	
	/**
     * @ORM\ManyToOne(targetEntity="CCDNUser\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_sent_to_user_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $sentTo = null;
	
	/**
	 * @ORM\Column(type="text", name="send_to")
	 */
	protected $sendTo;
	
	/**
     * @ORM\ManyToOne(targetEntity="CCDNUser\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_from_user_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $sentFrom = null;

	/**
     * @ORM\ManyToOne(targetEntity="CCDNUser\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_owned_by_user_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $ownedBy = null;
	
	/**
     * @ORM\Column(type="boolean", name="is_draft", nullable=false)
     */
	protected $isDraft = false;
	
	/**
	 * @ORM\Column(type="boolean", name="is_read", nullable=false)
	 */
	protected $isRead = false;
	
	/**
	 * @ORM\Column(type="boolean", name="is_flagged", nullable=false)
	 */
	protected $isFlagged = false;
	
	/***
     * @ORM\ManyToOne(targetEntity="CCDNMessage\MessageBundle\Entity\Message", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_in_response_message_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $inResponseTo = null;
	
	/**
     * @ORM\ManyToOne(targetEntity="CCDNComponent\AttachmentBundle\Entity\Attachment", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_attachment_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $attachment = null;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set subject
     *
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * Get subject
     *
     * @return string 
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set body
     *
     * @param text $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * Get body
     *
     * @return text 
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set sentDate
     *
     * @param datetime $sentDate
     */
    public function setSentDate($sentDate)
    {
        $this->sentDate = $sentDate;
    }

    /**
     * Get sentDate
     *
     * @return datetime 
     */
    public function getSentDate()
    {
        return $this->sentDate;
    }

    /**
     * Set createdDate
     *
     * @param datetime $createdDate
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;
    }

    /**
     * Get createdDate
     *
     * @return datetime 
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * Set sendTo
     *
     * @param text $sendTo
     */
    public function setSendTo($sendTo)
    {
        $this->sendTo = $sendTo;
    }

    /**
     * Get sendTo
     *
     * @return text 
     */
    public function getSendTo()
    {
        return $this->sendTo;
    }

    /**
     * Set isDraft
     *
     * @param boolean $isDraft
     */
    public function setIsDraft($isDraft)
    {
        $this->isDraft = $isDraft;
    }

    /**
     * Get isDraft
     *
     * @return boolean 
     */
    public function getIsDraft()
    {
        return $this->isDraft;
    }

    /**
     * Set isRead
     *
     * @param boolean $isRead
     */
    public function setIsRead($isRead)
    {
        $this->isRead = $isRead;
    }

    /**
     * Get isRead
     *
     * @return boolean 
     */
    public function getIsRead()
    {
        return $this->isRead;
    }

    /**
     * Set isFlagged
     *
     * @param boolean $isFlagged
     */
    public function setIsFlagged($isFlagged)
    {
        $this->isFlagged = $isFlagged;
    }

    /**
     * Get isFlagged
     *
     * @return boolean 
     */
    public function getIsFlagged()
    {
        return $this->isFlagged;
    }

    /**
     * Set folder
     *
     * @param CCDNMessage\MessageBundle\Entity\Folder $folder
     */
    public function setFolder(\CCDNMessage\MessageBundle\Entity\Folder $folder = null)
    {
        $this->folder = $folder;
    }

    /**
     * Get folder
     *
     * @return CCDNMessage\MessageBundle\Entity\Folder 
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * Set sentTo
     *
     * @param CCDNUser\UserBundle\Entity\User $sentTo
     */
    public function setSentTo(\CCDNUser\UserBundle\Entity\User $sentTo = null)
    {
        $this->sentTo = $sentTo;
    }

    /**
     * Get sentTo
     *
     * @return CCDNUser\UserBundle\Entity\User 
     */
    public function getSentTo()
    {
        return $this->sentTo;
    }

    /**
     * Set sentFrom
     *
     * @param CCDNUser\UserBundle\Entity\User $sentFrom
     */
    public function setSentFrom(\CCDNUser\UserBundle\Entity\User $sentFrom = null)
    {
        $this->sentFrom = $sentFrom;
    }

    /**
     * Get sentFrom
     *
     * @return CCDNUser\UserBundle\Entity\User 
     */
    public function getSentFrom()
    {
        return $this->sentFrom;
    }

    /**
     * Set ownedBy
     *
     * @param CCDNUser\UserBundle\Entity\User $ownedBy
     */
    public function setOwnedBy(\CCDNUser\UserBundle\Entity\User $ownedBy = null)
    {
        $this->ownedBy = $ownedBy;
    }

    /**
     * Get ownedBy
     *
     * @return CCDNUser\UserBundle\Entity\User 
     */
    public function getOwnedBy()
    {
        return $this->ownedBy;
    }

    /**
     * Set attachment
     *
     * @param CCDNComponent\AttachmentBundle\Entity\Attachment $attachment
     */
    public function setAttachment(\CCDNComponent\AttachmentBundle\Entity\Attachment $attachment = null)
    {
        $this->attachment = $attachment;
    }

    /**
     * Get attachment
     *
     * @return CCDNComponent\AttachmentBundle\Entity\Attachment 
     */
    public function getAttachment()
    {
        return $this->attachment;
    }
}