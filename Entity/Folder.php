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

use CCDNUser\UserBundle\Entity\User;

/**
 * @ORM\Entity(repositoryClass="CCDNMessage\MessageBundle\Repository\FolderRepository")
 * @ORM\Table(name="CC_Message_Folder")
 */
class Folder
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="text")
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="CCDNUser\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_owned_by_user_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $ownedBy = null;

    // either 1=Inbox, 2=Sent, 3=Drafts or 4=Junk, 5=Deleted.
    /**
     * @ORM\Column(type="integer", name="special_type", nullable=true)
     */
    protected $specialType;

    /**
     * @ORM\Column(type="integer", name="cached_read_count", nullable=true)
     */
    protected $cachedReadCount = 0;

    /**
     * @ORM\Column(type="integer", name="cached_unread_count", nullable=true)
     */
    protected $cachedUnreadCount = 0;

    /**
     * @ORM\Column(type="integer", name="cached_total_message_count", nullable=true)
     */
    protected $cachedTotalMessageCount = 0;

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
     * Set name
     *
     * @param text $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return text
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set specialType
     *
     * @param integer $specialType
     */
    public function setSpecialType($specialType)
    {
        $this->specialType = $specialType;
    }

    /**
     * Get specialType
     *
     * @return integer
     */
    public function getSpecialType()
    {
        return $this->specialType;
    }

    /**
     * Set cachedReadCount
     *
     * @param integer $cachedReadCount
     */
    public function setCachedReadCount($cachedReadCount)
    {
        $this->cachedReadCount = $cachedReadCount;
    }

    /**
     * Get cachedReadCount
     *
     * @return integer
     */
    public function getCachedReadCount()
    {
        return $this->cachedReadCount;
    }

    /**
     * Set cachedUnreadCount
     *
     * @param integer $cachedUnreadCount
     */
    public function setCachedUnreadCount($cachedUnreadCount)
    {
        $this->cachedUnreadCount = $cachedUnreadCount;
    }

    /**
     * Get cachedUnreadCount
     *
     * @return integer
     */
    public function getCachedUnreadCount()
    {
        return $this->cachedUnreadCount;
    }

    /**
     * Set cachedTotalMessageCount
     *
     * @param integer $cachedTotalMessageCount
     */
    public function setCachedTotalMessageCount($cachedTotalMessageCount)
    {
        $this->cachedTotalMessageCount = $cachedTotalMessageCount;
    }

    /**
     * Get cachedTotalMessageCount
     *
     * @return integer
     */
    public function getCachedTotalMessageCount()
    {
        return $this->cachedTotalMessageCount;
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
}
