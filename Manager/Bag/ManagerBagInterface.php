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

namespace CCDNMessage\MessageBundle\Manager\Bag;

use CCDNMessage\MessageBundle\Manager\Bag\ManagerBagInterface;

use Symfony\Component\DependencyInjection\Container;

/**
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
interface ManagerBagInterface
{
	/**
	 *
	 * @access public
	 * @param \Symfony\Component\DependencyInjection\Container $container
	 */
    public function __construct(Container $container);
	
	/**
	 *
	 * @access public
	 * @return \CCDNMessage\MessageBundle\Manager\FolderManager
	 */
	public function getFolderManager();
	
	/**
	 *
	 * @access public
	 * @return \CCDNMessage\MessageBundle\Manager\MessageManager
	 */
	public function getMessageManager();
	
	/**
	 *
	 * @access public
	 * @return \CCDNMessage\MessageBundle\Manager\RegistryManager
	 */
	public function getRegistryManager();
	
	/**
	 *
	 * @access public
	 * @return int
	 */
	public function getMessagesPerPageOnFolders();
	
	/**
	 *
	 * @access public
	 * @return \CCDNMessage\MessageBundle\Manager\ThreadManager
	 */
	public function getThreadManager();
	
	/**
	 *
	 * @access public
	 * @return \CCDNMessage\MessageBundle\Manager\UserManager
	 */
	public function getUserManager();
}