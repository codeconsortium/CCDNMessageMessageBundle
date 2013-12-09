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

namespace CCDNMessage\MessageBundle\Model\FrontModel;

use Symfony\Component\Security\Core\User\UserInterface;

use CCDNMessage\MessageBundle\Model\FrontModel\BaseModel;
use CCDNMessage\MessageBundle\Model\FrontModel\ModelInterface;

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
class FolderModel extends BaseModel implements ModelInterface
{
	/**
	 * 
	 * @access public
	 * @return \CCDNMessage\MessageBundle\Entity\Folder
	 */
	public function createFolder()
	{
		return $this->getManager()->createFolder();
	}

    /**
     *
     * @access public
     * @param  int                                          $userId
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findAllFoldersForUserById($userId)
    {
		return $this->getRepository()->findAllFoldersForUserById($userId);
    }

	/**
	 * 
	 * @access public
	 * @param  \CCDNMessage\MessageBundle\Entity\Folder               $folder
	 * @return \CCDNMessage\MessageBundle\Model\Component\Manager\FolderManager
	 */
	public function saveFolder(Folder $folder)
	{
		return $this->getManager()->saveFolder($folder);
	}

    /**
     *
     * @access public
     * @param  \Symfony\Component\Security\Core\User\UserInterface    $user
     * @return \CCDNMessage\MessageBundle\Model\Component\Manager\FolderManager
     */
    public function setupDefaults(UserInterface $user)
    {
        return $this->getManager()->setupDefaults($user);
    }
}