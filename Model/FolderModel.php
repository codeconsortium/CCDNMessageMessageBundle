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
    protected $ownedBy = null;
	
    /**
	 * @var \Doctrine\Common\Collections\ArrayCollection $messages
	 */
	protected $messages;
	
	/**
	 *
	 * @access public
	 */
    public function __construct()
    {
        // your own logic
    }

    /**
     * Get ownedBy
     *
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    public function getOwnedBy()
    {
        return $this->ownedBy;
    }
	
    /**
     * Set ownedBy
     *
     * @param \Symfony\Component\Security\Core\User\UserInterface $ownedBy
	 * @return \CCDNMessage\MessageBundle\Entity\Folder
     */
    public function setOwnedBy(UserInterface $ownedBy = null)
    {
        $this->ownedBy = $ownedBy;
		
		return $this;
    }
	
    /**
     * Get ownedBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
