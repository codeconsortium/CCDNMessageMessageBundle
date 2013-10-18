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

namespace CCDNMessage\MessageBundle\Model\Manager\Bag;

use CCDNMessage\MessageBundle\Model\Manager\Bag\ManagerBagInterface;

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
