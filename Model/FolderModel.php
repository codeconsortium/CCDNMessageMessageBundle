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

namespace CCDNMessage\MessageBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;

abstract class FolderModel
{
    /**
	 * @var \Symfony\Component\Security\Core\User\UserInterface $ownedBy
	 */
    protected $ownedByUser = null;
	
    /**
	 * @var \Doctrine\Common\Collections\ArrayCollection $envelopes
	 */
	protected $envelopes;
	
	/**
	 *
	 * @access public
	 */
    public function __construct()
    {
        // your own logic
    }

    /**
     * Get ownedByUser
     *
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    public function getOwnedByUser()
    {
        return $this->ownedByUser;
    }
	
    /**
     * Set ownedByUser
     *
     * @param \Symfony\Component\Security\Core\User\UserInterface $ownedBy
	 * @return \CCDNMessage\MessageBundle\Entity\Folder
     */
    public function setOwnedByUser(UserInterface $ownedByUser = null)
    {
        $this->ownedByUser = $ownedByUser;
		
		return $this;
    }
	
    /**
     * Get envelopes
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getEnvelopes()
    {
        return $this->envelopes;
    }
}