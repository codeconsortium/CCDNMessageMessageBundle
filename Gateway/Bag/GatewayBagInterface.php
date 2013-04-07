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

namespace CCDNMessage\MessageBundle\Gateway\Bag;

use CCDNMessage\MessageBundle\Gateway\Bag\GatewayBagInterface;

use Symfony\Component\DependencyInjection\Container;

/**
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
interface GatewayBagInterface
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
	 * @return \CCDNMessage\MessageBundle\Gateway\FolderGateway
	 */
	public function getFolderGateway();
	
	/**
	 *
	 * @access public
	 * @return \CCDNMessage\MessageBundle\Gateway\MessageGateway
	 */
	public function getMessageGateway();
	
	/**
	 *
	 * @access public
	 * @return \CCDNMessage\MessageBundle\Gateway\RegistryGateway
	 */
	public function getRegistryGateway();
	
	/**
	 *
	 * @access public
	 * @return \CCDNMessage\MessageBundle\Gateway\ThreadGateway
	 */
	public function getThreadGateway();
	
	/**
	 *
	 * @access public
	 * @return \CCDNMessage\MessageBundle\Gateway\UserGateway
	 */
	public function getUserGateway();
}