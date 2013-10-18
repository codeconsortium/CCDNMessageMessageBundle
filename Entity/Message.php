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

use CCDNMessage\MessageBundle\Entity\Model\MessageModel;

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
class Message extends MessageModel
{
    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var string $sendTo
     */
    protected $sendTo;

    /**
     * @var string $name
     */
    protected $subject;

    /**
     * @var string $name
     */
    protected $body;

    /**
     * @var \DateTime $createdDate
     */
    protected $createdDate;

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
     * @param  text    $sendTo
     * @return Message
     */
    public function setSendTo($sendTo)
    {
        $this->sendTo = $sendTo;

        return $this;
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
     * @param  string  $subject
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
     * @param  text    $body
     * @return Message
     */
    public function setBody($body)
    {
        $this->body = $body;

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
     * @param  datetime $createdDate
     * @return Message
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;

        return $this;
    }
}
