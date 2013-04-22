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
 * @category CCDNMessage
 * @package  MessageBundle
 *
 * @author   Reece Fowell <reece@codeconsortium.com>
 * @license  http://opensource.org/licenses/MIT MIT
 * @version  Release: 2.0
 * @link     https://github.com/codeconsortium/CCDNMessageMessageBundle
 *
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
