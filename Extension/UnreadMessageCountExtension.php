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

namespace CCDNMessage\MessageBundle\Extension;

/**
 * 
 * @author Reece Fowell <reece@codeconsortium.com> 
 * @version 1.0
 */
class UnreadMessageCountExtension extends \Twig_Extension
{


	/**
	 *
	 * @access protected 
	 */
	protected $container;
	
	
	/**
	 *
	 */
	public function __construct($container)
	{
		$this->container = $container;
	}
	
	
	/**
	 * 
	 * @access public
	 * @return Array()
	 */
	public function getFunctions()
	{
		return array(
			'unreadMessageCount' => new \Twig_Function_Method($this, 'unreadMessageCount'),
		);
	}
	
	
	/**
	 * Gets the number of unread messages that is cached in the message registry.
	 *
	 * @access public
	 * @param object $user
	 * @return int
	 */
	public function unreadMessageCount()
	{
		$user = $this->container->get('security.context')->getToken()->getUser();
		
		$unreadMessageCount = $this->container->get('ccdn_message_message.registry.repository')->findRegistryRecordForUser($user->getId());
		
		return $unreadMessageCount->getCacheUnreadMessagesCount();
	}
	
	
	/**
	 *
	 * @access public
	 * @return string
	 */
	public function getName()
	{
		return 'unreadMessageCount';
	}
	
}