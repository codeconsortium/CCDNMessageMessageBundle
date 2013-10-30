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

use CCDNMessage\MessageBundle\Entity\Model\EnvelopeModel;

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
class Envelope extends EnvelopeModel
{
    /**
     *
     * @var integer $id
     */
    protected $id;

    /**
     *
     * @var \DateTime $sentDate
     */
    protected $sentDate;

    /**
     *
     * @var Boolean $isDraft
     */
    protected $isDraft = false;

    /**
     *
     * @var Boolean $isRead
     */
    protected $isRead = false;

    /**
     *
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
     * @param  datetime $sentDate
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
    public function isDraft()
    {
        return $this->isDraft;
    }

    /**
     * Set isDraft
     *
     * @param  boolean $isDraft
     * @return Message
     */
    public function setDraft($isDraft)
    {
        $this->isDraft = $isDraft;

        return $this;
    }

    /**
     * Get isRead
     *
     * @return boolean
     */
    public function isRead()
    {
        return $this->isRead;
    }

    /**
     * Set isRead
     *
     * @param  boolean $isRead
     * @return Message
     */
    public function setRead($isRead)
    {
        $this->isRead = $isRead;

        return $this;
    }

    /**
     * Get isFlagged
     *
     * @return boolean
     */
    public function isFlagged()
    {
        return $this->isFlagged;
    }

    /**
     * Set isFlagged
     *
     * @param  boolean $isFlagged
     * @return Message
     */
    public function setFlagged($isFlagged)
    {
        $this->isFlagged = $isFlagged;

        return $this;
    }
}
