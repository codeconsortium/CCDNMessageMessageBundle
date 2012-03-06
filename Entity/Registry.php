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
     * @ORM\JoinColumn(name="owned_by_user_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $owned_by;
		
	/**
     * @ORM\Column(type="integer")
     */
    protected $cacheUnreadMessagesCount;


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
     * Set owned_by
     *
     * @param CCDNUser\UserBundle\Entity\User $ownedBy
     * @return Registry
     */
    public function setOwnedBy(\CCDNUser\UserBundle\Entity\User $ownedBy = null)
    {
        $this->owned_by = $ownedBy;
        return $this;
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
     * Set cacheUnreadMessagesCount
     *
     * @param integer $cacheUnreadMessagesCount
     * @return Registry
     */
    public function setCacheUnreadMessagesCount($cacheUnreadMessagesCount)
    {
        $this->cacheUnreadMessagesCount = $cacheUnreadMessagesCount;
        return $this;
    }

    /**
     * Get cacheUnreadMessagesCount
     *
     * @return integer 
     */
    public function getCacheUnreadMessagesCount()
    {
        return $this->cacheUnreadMessagesCount;
    }
}