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

namespace CCDNMessage\MessageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use CCDNMessage\MessageBundle\Model\MessageModel;

class Message extends MessageModel
{
    /** @var integer $id */
    protected $id;

    /** @var string $name */
    protected $subject;

    /** @var string $name */
    protected $body;

    /** @var \DateTime $sentDate */
    protected $sentDate;

    /** @var \DateTime $createdDate */
    protected $createdDate;

    /** @var string $sendTo */
    protected $sendTo;

    /** @var Boolean $isDraft */
    protected $isDraft = false;

    /** @var Boolean $isRead */
    protected $isRead = false;

    /** @var Boolean $isFlagged */
    protected $isFlagged = false;


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
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }
	
    /**
     * Set subject
     *
     * @param string $subject
	 * @return Message
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
		
		return $this;
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
     * Set body
     *
     * @param text $body
	 * @return Message
     */
    public function setBody($body)
    {
        $this->body = $body;
		
		return $this;
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
     * Set sentDate
     *
     * @param datetime $sentDate
	 * @return Message
     */
    public function setSentDate($sentDate)
    {
        $this->sentDate = $sentDate;
		
		return $this;
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
     * Set createdDate
     *
     * @param datetime $createdDate
	 * @return Message
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;
		
		return $this;
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
     * Set sendTo
     *
     * @param text $sendTo
	 * @return Message
     */
    public function setSendTo($sendTo)
    {
        $this->sendTo = $sendTo;
		
		return $this;
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
     * Set isDraft
     *
     * @param boolean $isDraft
	 * @return Message
     */
    public function setIsDraft($isDraft)
    {
        $this->isDraft = $isDraft;
		
		return $this;
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
     * Set isRead
     *
     * @param boolean $isRead
	 * @return Message
     */
    public function setIsRead($isRead)
    {
        $this->isRead = $isRead;
		
		return $this;
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
     * Set isFlagged
     *
     * @param boolean $isFlagged
	 * @return Message
     */
    public function setIsFlagged($isFlagged)
    {
        $this->isFlagged = $isFlagged;
		
		return $this;
    }
}