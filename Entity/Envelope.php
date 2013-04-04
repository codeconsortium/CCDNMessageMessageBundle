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

use CCDNMessage\MessageBundle\Model\EnvelopeModel;

class Envelope extends EnvelopeModel
{
	/** 
	 * @var integer $id
	 */
    protected $id;
	
    /**
	 * @var \DateTime $sentDate
	 */
    protected $sentDate;
    
	/**
	 * @var Boolean $isDraft
	 */
    protected $isDraft = false;

    /**
	 * @var Boolean $isRead
	 */
    protected $isRead = false;

    /**
	 * @var Boolean $isFlagged
	 */
    protected $isFlagged = false;
	
	/**
	 *
	 * @access public
	 */
    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

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