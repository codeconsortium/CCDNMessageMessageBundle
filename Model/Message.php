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

abstract class Message
{
	/** @var CCDNMessage\MessageBundle\Entity\Folder $folder */
    protected $folder = null;

	/** @var UserInterface $sentTo */
    protected $sentTo = null;
	
	/** @var UserInterface $sentFrom */
    protected $sentFrom = null;
	
	/** @var UserInterface $owmedBy */
    protected $ownedBy = null;

	/** @var CCDNMessage\MessageBundle\Entity\Message $inResponseTo */
    protected $inResponseTo = null;
	
	
    public function __construct()
    {
    }
	
	
    /**
     * Set folder
     *
     * @param CCDNMessage\MessageBundle\Entity\Folder $folder
     */
    public function setFolder(\CCDNMessage\MessageBundle\Entity\Folder $folder = null)
    {
        $this->folder = $folder;
    }

    /**
     * Get folder
     *
     * @return CCDNMessage\MessageBundle\Entity\Folder
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * Set sentTo
     *
     * @param UserInterface $sentTo
     */
    public function setSentTo(UserInterface $sentTo = null)
    {
        $this->sentTo = $sentTo;
    }

    /**
     * Get sentTo
     *
     * @return UserInterface
     */
    public function getSentTo()
    {
        return $this->sentTo;
    }

    /**
     * Set sentFrom
     *
     * @param UserInterface $sentFrom
     */
    public function setSentFrom(UserInterface $sentFrom = null)
    {
        $this->sentFrom = $sentFrom;
    }

    /**
     * Get sentFrom
     *
     * @return UserInterface
     */
    public function getSentFrom()
    {
        return $this->sentFrom;
    }

    /**
     * Set ownedBy
     *
     * @param UserInterface $ownedBy
     */
    public function setOwnedBy(UserInterface $ownedBy = null)
    {
        $this->ownedBy = $ownedBy;
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
	
    /**
     * Set inResponseTo
     *
     * @param CCDNMessage\MessageBundle\Entity\Message $inResponseTo
     */
	public function setInResponseTo(\CCDNMessage\MessageBundle\Entity\Message $inResponseTo)
	{
		$this->inResponseTo = $inResponseTo;
	}
	
    /**
     * Get inResponseTo
     *
     * @return CCDNMessage\MessageBundle\Entity\Message
     */
	public function getInResponseTo()
	{
		return $this->inResponseTo;
	}
}
