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

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class Registry
{
	/** @var UserInterface $ownedBy */
    protected $ownedBy = null;

	
    public function __construct()
    {
    }

	
    /**
     * Set ownedBy
     *
     * @param UserInterface $ownedBy
     */
    public function setOwnedBy(UserInterface $ownedBy = null)
    {
        $this->ownedBy = $ownedBy;
		
		return $this;
    }

    /**
     * Get ownedBy
     *
     * @return UserInterface
     */
    public function getOwnedBy()
    {
        return $this->ownedBy;
    }
}
