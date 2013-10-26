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
class FolderHelper
{
    /**
     *
     * @access public
     * @param  array $folders
     * @return int
     */
    public function filterFolderBySpecialType($folders, $specialType)
    {
		foreach ($folders as $folder) {
			if ($folder->isSpecialType($specialType)) {
				return $folder;
			}
		}
		
		return null;
    }
	
    /**
     *
     * @access public
     * @param  array $folders
     * @return int
     */
    public function filterFolderByName($folders, $name)
    {
		foreach ($folders as $folder) {
			if ($folder->getName() == $name) {
				return $folder;
			}
		}
		
		return null;
	}
}
