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
	 * @Assert\NotBlank()
     */
    protected $name;
	
	/**
     * @ORM\ManyToOne(targetEntity="CCDNUser\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(name="owned_by_user_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $owned_by;
	
	// either 1=Inbox, 2=Sent, 3=Drafts or 4=Junk, 5=Deleted.
	/**
	 * @ORM\Column(type="integer", nullable=true)
     */
	protected $special_type;

	/**
	 * @ORM\Column(type="integer", nullable=true)
     */
	protected $cache_read_count;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
     */
	protected $cache_unread_count;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
     */
	protected $cache_total_message_count;
	


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
     * Set special_type
     *
     * @param integer $specialType
     */
    public function setSpecialType($specialType)
    {
        $this->special_type = $specialType;
    }

    /**
     * Get special_type
     *
     * @return integer 
     */
    public function getSpecialType()
    {
        return $this->special_type;
    }

    /**
     * Set cache_read_count
     *
     * @param integer $cacheReadCount
     */
    public function setCacheReadCount($cacheReadCount)
    {
        $this->cache_read_count = $cacheReadCount;
    }

    /**
     * Get cache_read_count
     *
     * @return integer 
     */
    public function getCacheReadCount()
    {
        return $this->cache_read_count;
    }

    /**
     * Set cache_unread_count
     *
     * @param integer $cacheUnreadCount
     */
    public function setCacheUnreadCount($cacheUnreadCount)
    {
        $this->cache_unread_count = $cacheUnreadCount;
    }

    /**
     * Get cache_unread_count
     *
     * @return integer 
     */
    public function getCacheUnreadCount()
    {
        return $this->cache_unread_count;
    }

    /**
     * Set cache_total_message_count
     *
     * @param integer $cacheTotalMessageCount
     */
    public function setCacheTotalMessageCount($cacheTotalMessageCount)
    {
        $this->cache_total_message_count = $cacheTotalMessageCount;
    }

    /**
     * Get cache_total_message_count
     *
     * @return integer 
     */
    public function getCacheTotalMessageCount()
    {
        return $this->cache_total_message_count;
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
}