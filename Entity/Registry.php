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
 * @ORM\Entity(repositoryClass="CCDNMessage\MessageBundle\Repository\RegistryRepository")
 * @ORM\Table(name="CC_Message_Registry")
 */
class Registry
{
	
	/**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
	protected $id;

	/**
     * @ORM\ManyToOne(targetEntity="CCDNUser\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_owned_by_user_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $ownedBy = null;
		
	/**
     * @ORM\Column(type="integer", name="cached_unread_message_count")
     */
    protected $cachedUnreadMessagesCount = 0;


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
     * Set cachedUnreadMessagesCount
     *
     * @param integer $cachedUnreadMessagesCount
     */
    public function setCachedUnreadMessagesCount($cachedUnreadMessagesCount)
    {
        $this->cachedUnreadMessagesCount = $cachedUnreadMessagesCount;
    }

    /**
     * Get cachedUnreadMessagesCount
     *
     * @return integer 
     */
    public function getCachedUnreadMessagesCount()
    {
        return $this->cachedUnreadMessagesCount;
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