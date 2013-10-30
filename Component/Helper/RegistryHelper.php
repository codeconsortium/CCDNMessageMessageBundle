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

namespace CCDNMessage\MessageBundle\Component\Helper;

use Symfony\Component\Security\Core\User\UserInterface;
use CCDNMessage\MessageBundle\Model\Model\RegistryModel;
use CCDNMessage\MessageBundle\Entity\Folder;

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
class RegistryHelper
{
	/**
	 * 
	 * @access protected
	 * @var \CCDNMessage\MessageBundle\Model\Model\RegistryModel $registryModel
	 */
	protected $registryModel;

	/**
	 * 
	 * @access public
	 * @param  \CCDNMessage\MessageBundle\Model\Model\RegistryModel $registryModel
	 */
	public function __construct(RegistryModel $registryModel)
	{
		$this->registryModel = $registryModel;
	}

    /**
     *
     * @access public
	 * @param  \Symfony\Component\Security\Core\User\UserInterface $user
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findOneRegistryForUserById(UserInterface $user)
    {
		$registry = $this->registryModel->findOneRegistryForUserById($user->getId());
		
        if (null == $registry) {
            $this->registryModel->setupDefaults($user);
        
            $registry = $this->registryModel->findOneRegistryForUserById($user->getId());
        }
		
		return $registry;
    }
}
