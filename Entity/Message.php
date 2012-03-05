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
use CCDNMessage\MessageBundle\Form\Validator\Constraints as CCDNMessageAssert;

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
	 * @Assert\NotBlank()
     */
    protected $subject;
	
	/**
     * @ORM\Column(type="text")
	 * @Assert\NotBlank()
     */
	protected $body;

	/**
     * @ORM\ManyToOne(targetEntity="CCDNMessage\MessageBundle\Entity\Folder", cascade={"persist"})
     * @ORM\JoinColumn(name="in_folder_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $folder;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	protected $sent_date;
	
	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $created_date;
	
	/**
     * @ORM\ManyToOne(targetEntity="CCDNUser\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(name="sent_to_user_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $sent_to;
	
	/**
	 * @ORM\Column(type="text")
	 * @CCDNMessageAssert\SendTo()
	 */
	protected $send_to;
	
	/**
     * @ORM\ManyToOne(targetEntity="CCDNUser\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(name="from_user_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $sent_from;

	/**
     * @ORM\ManyToOne(targetEntity="CCDNUser\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(name="owned_by_user_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $owned_by;
	
	/**
     * @ORM\Column(type="boolean")
     */
	protected $is_draft;
	
	/**
	 * @ORM\Column(type="boolean")
	 */
	protected $read_it;
	
	/**
	 * @ORM\Column(type="boolean", nullable=true)
	 */
	protected $flagged;
	
	/***
     * @ORM\ManyToOne(targetEntity="CCDNMessage\MessageBundle\Entity\Message", cascade={"persist"})
     * @ORM\JoinColumn(name="in_response_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $inResponseTo;
	
	/**
     * @ORM\ManyToOne(targetEntity="CCDNComponent\AttachmentBundle\Entity\Attachment", cascade={"persist"})
     * @ORM\JoinColumn(name="attachment_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $attachment;
	
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
     * @param text $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * Get subject
     *
     * @return text 
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
     * Set sent_date
     *
     * @param datetime $sentDate
     */
    public function setSentDate($sentDate)
    {
        $this->sent_date = $sentDate;
    }

    /**
     * Get sent_date
     *
     * @return datetime 
     */
    public function getSentDate()
    {
        return $this->sent_date;
    }

    /**
     * Set created_date
     *
     * @param datetime $createdDate
     */
    public function setCreatedDate($createdDate)
    {
        $this->created_date = $createdDate;
    }

    /**
     * Get created_date
     *
     * @return datetime 
     */
    public function getCreatedDate()
    {
        return $this->created_date;
    }

    /**
     * Set is_draft
     *
     * @param boolean $isDraft
     */
    public function setIsDraft($isDraft)
    {
        $this->is_draft = $isDraft;
    }

    /**
     * Get is_draft
     *
     * @return boolean 
     */
    public function getIsDraft()
    {
        return $this->is_draft;
    }

    /**
     * Set folder
     *
     * @param CCDNMessage\MessageBundle\Entity\Folder $folder
     */
    public function setFolder(\CCDNMessage\MessageBundle\Entity\Folder $folder)
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
     * Set sent_to
     *
     * @param CCDNUser\UserBundle\Entity\User $sentTo
     */
    public function setSentTo(\CCDNUser\UserBundle\Entity\User $sentTo)
    {
        $this->sent_to = $sentTo;
    }

    /**
     * Get sent_to
     *
     * @return CCDNUser\UserBundle\Entity\User 
     */
    public function getSentTo()
    {
        return $this->sent_to;
    }

    /**
     * Set sent_from
     *
     * @param CCDNUser\UserBundle\Entity\User $sentFrom
     */
    public function setSentFrom(\CCDNUser\UserBundle\Entity\User $sentFrom)
    {
        $this->sent_from = $sentFrom;
    }

    /**
     * Get sent_from
     *
     * @return CCDNUser\UserBundle\Entity\User 
     */
    public function getSentFrom()
    {
        return $this->sent_from;
    }

    /**
     * Set owned_by
     *
     * @param CCDNUser\UserBundle\Entity\User $ownedBy
     */
    public function setOwnedBy(\CCDNUser\UserBundle\Entity\User $ownedBy)
    {
        $this->owned_by = $ownedBy;
    }

    /**
     * Get owned_by
     *
     * @return CCDNUser\UserBundle\Entity\User 
     */
    public function getOwnedBy()
    {
        return $this->owned_by;
    }

    /**
     * Set read
     *
     * @param boolean $read
     */
    public function setRead($read)
    {
        $this->read = $read;
    }

    /**
     * Get read
     *
     * @return boolean 
     */
    public function getRead()
    {
        return $this->read;
    }

    /**
     * Set flagged
     *
     * @param boolean $flagged
     */
    public function setFlagged($flagged)
    {
        $this->flagged = $flagged;
    }

    /**
     * Get flagged
     *
     * @return boolean 
     */
    public function getFlagged()
    {
        return $this->flagged;
    }

    /**
     * Set read_it
     *
     * @param boolean $readIt
     */
    public function setReadIt($readIt)
    {
        $this->read_it = $readIt;
    }

    /**
     * Get read_it
     *
     * @return boolean 
     */
    public function getReadIt()
    {
        return $this->read_it;
    }

    /**
     * Set send_to
     *
     * @param text $sendTo
     */
    public function setSendTo($sendTo)
    {
        $this->send_to = $sendTo;
    }

    /**
     * Get send_to
     *
     * @return text 
     */
    public function getSendTo()
    {
        return $this->send_to;
    }

    /**
     * Set attachment
     *
     * @param CCDNComponent\AttachmentBundle\Entity\Attachment $attachment
     * @return Message
     */
    public function setAttachment(\CCDNComponent\AttachmentBundle\Entity\Attachment $attachment = null)
    {
        $this->attachment = $attachment;
        return $this;
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