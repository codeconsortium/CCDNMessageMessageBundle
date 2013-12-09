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

namespace CCDNMessage\MessageBundle\Model\Component\Manager;

use Symfony\Component\Security\Core\User\UserInterface;

use CCDNMessage\MessageBundle\Model\Component\Manager\ManagerInterface;
use CCDNMessage\MessageBundle\Model\Component\Manager\BaseManager;

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
class FolderManager extends BaseManager implements ManagerInterface
{
	/**
	 * 
	 * @access public
	 * @return \CCDNMessage\MessageBundle\Entity\Folder
	 */
	public function createFolder()
	{
		return $this->gateway->createFolder();
	}

	public function saveFolder(Folder $folder)
	{
		$this->persist($folder);
		$this->flush();
		$this->refresh($folder);
		
		return $folder;
	}

    /**
     *
     * @access public
     * @param  \Symfony\Component\Security\Core\User\UserInterface    $user
     * @return \CCDNMessage\MessageBundle\Model\Component\Manager\FolderManager
     */
    public function setupDefaults(UserInterface $user)
    {
        if (! is_object($user) || ! $user instanceof UserInterface) {
            throw new \Exception('User cannot be null and must implement UserInterface!');
        }

        $folderNames = Folder::$defaultSpecialTypes;
		
        foreach ($folderNames as $key => $folderName) {
            $folder = new Folder();
            $folder->setOwnedByUser($user);
            $folder->setName($folderName);
            $folder->setSpecialType($key);
            $folder->setCachedReadCount(0);
            $folder->setCachedUnreadCount(0);
            $folder->setCachedTotalMessageCount(0);

            $this->persist($folder);
        }

		$this->flush();
		
		return $this;
    }
}
