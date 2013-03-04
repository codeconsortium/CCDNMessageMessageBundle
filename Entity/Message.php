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

use CCDNMessage\MessageBundle\Model\Message as AbstractMessage;

class Message extends AbstractMessage
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
}
