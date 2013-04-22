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
class GatewayBag implements GatewayBagInterface
{
    /**
     *
     * @access protected
     * @var \CCDNMessage\MessageBundle\Gateway\FolderGateway $folderGateway
     */
    protected $folderGateway;

    /**
     *
     * @access protected
     * @var \CCDNMessage\MessageBundle\Gateway\EnvelopeGateway $envelopeGateway
     */
    protected $envelopeGateway;

    /**
     *
     * @access protected
     * @var \CCDNMessage\MessageBundle\Gateway\MessageGateway $messageGateway
     */
    protected $messageGateway;

    /**
     *
     * @access protected
     * @var \CCDNMessage\MessageBundle\Gateway\ThreadGateway $threadGateway
     */
    protected $threadGateway;

    /**
     *
     * @access protected
     * @var \CCDNMessage\MessageBundle\Gateway\RegistryGateway $registryGateway
     */
    protected $registryGateway;

    /**
     *
     * @access protected
     * @var \CCDNMessage\MessageBundle\Gateway\UserGateway $userGateway
     */
    protected $userGateway;

    /**
     *
     * @access protected
     * @var \Symfony\Component\DependencyInjection\Container $container
     */
    protected $container;

    /**
     *
     * @access public
     * @param \Symfony\Component\DependencyInjection\Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     *
     * @access public
     * @return \CCDNMessage\MessageBundle\Gateway\FolderGateway
     */
    public function getFolderGateway()
    {
        if (null == $this->folderGateway) {
            $this->folderGateway = $this->container->get('ccdn_message_message.gateway.folder');
        }

        return $this->folderGateway;
    }

    /**
     *
     * @access public
     * @return \CCDNMessage\MessageBundle\Gateway\EnvelopeGateway
     */
    public function getEnvelopeGateway()
    {
        if (null == $this->envelopeGateway) {
            $this->envelopeGateway = $this->container->get('ccdn_message_message.gateway.envelope');
        }

        return $this->envelopeGateway;
    }

    /**
     *
     * @access public
     * @return \CCDNMessage\MessageBundle\Gateway\MessageGateway
     */
    public function getMessageGateway()
    {
        if (null == $this->messageGateway) {
            $this->messageGateway = $this->container->get('ccdn_message_message.gateway.message');
        }

        return $this->messageGateway;
    }

    /**
     *
     * @access public
     * @return \CCDNMessage\MessageBundle\Gateway\ThreadGateway
     */
    public function getThreadGateway()
    {
        if (null == $this->threadGateway) {
            $this->threadGateway = $this->container->get('ccdn_message_message.gateway.thread');
        }

        return $this->threadGateway;
    }

    /**
     *
     * @access public
     * @return \CCDNMessage\MessageBundle\Gateway\RegistryGateway
     */
    public function getRegistryGateway()
    {
        if (null == $this->registryGateway) {
            $this->registryGateway = $this->container->get('ccdn_message_message.gateway.registry');
        }

        return $this->registryGateway;
    }

    /**
     *
     * @access public
     * @return \CCDNMessage\MessageBundle\Gateway\UserGateway
     */
    public function getUserGateway()
    {
        if (null == $this->userGateway) {
            $this->userGateway = $this->container->get('ccdn_message_message.gateway.user');
        }

        return $this->userGateway;
    }
}
