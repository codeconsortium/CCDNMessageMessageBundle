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
class ManagerBag implements ManagerBagInterface
{
	/**
	 *
	 * @access protected
	 * @var \Symfony\Component\DependencyInjection\Container $container
	 */
    protected $container;
	
	/**
	 *
	 * @access protected
	 * @var \CCDNComponent\CommonBundle\Component\Provider\UserProviderInterface $userProvider
	 */
	protected $userProvider;
	
	/**
	 *
	 * @access protected
	 * @var \CCDNMessage\MessageBundle\Manager\FolderManager $folderManager
	 */
	protected $folderManager;
	
	/**
	 *
	 * @access protected
	 * @var \CCDNMessage\MessageBundle\Manager\EnvelopeManager $envelopeManager
	 */
	protected $envelopeManager;
	
	/**
	 *
	 * @access protected
	 * @var \CCDNMessage\MessageBundle\Manager\MessageManager $messageManager
	 */
	protected $messageManager;
	
	/**
	 *
	 * @access protected
	 * @var \CCDNMessage\MessageBundle\Manager\ThreadManager $threadManager
	 */
	protected $threadManager;
	
	/**
	 *
	 * @access protected
	 * @var \CCDNMessage\MessageBundle\Manager\RegistryManager $registryManager
	 */
	protected $registryManager;
	
	/**
	 *
	 * @access protected
	 * @var int $messagesPerPageOnFolders
	 */
	protected $messagesPerPageOnFolders;
	
	/**
	 *
	 * @access protected
	 * @var int $quotaMaxAllowanceForMessages
	 */
	protected $quotaMaxAllowanceForMessages;
	
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
	 * @return \CCDNComponent\CommonBundle\Component\Provider\UserProviderInterface
	 */
	public function getUserProvider()
	{
		if (null == $this->userProvider) {
			$this->userProvider = $this->container->get('ccdn_component_common.component.provider.user_provider');
		}
	
		return $this->userProvider;
	}
		
	/**
	 *
	 * @access public
	 * @return \CCDNMessage\MessageBundle\Manager\FolderManager
	 */
	public function getFolderManager()
	{
		if (null == $this->folderManager) {
			$this->folderManager = $this->container->get('ccdn_message_message.manager.folder');
		}
		
		return $this->folderManager;
	}
	
	/**
	 *
	 * @access public
	 * @return \CCDNMessage\MessageBundle\Manager\EnvelopeManager
	 */
	public function getEnvelopeManager()
	{
		if (null == $this->envelopeManager) {
			$this->envelopeManager = $this->container->get('ccdn_message_message.manager.envelope');
		}
		
		return $this->envelopeManager;
	}
	
	/**
	 *
	 * @access public
	 * @return \CCDNMessage\MessageBundle\Manager\MessageManager
	 */
	public function getMessageManager()
	{
		if (null == $this->messageManager) {
			$this->messageManager = $this->container->get('ccdn_message_message.manager.message');
		}
		
		return $this->messageManager;
	}
	
	/**
	 *
	 * @access public
	 * @return \CCDNMessage\MessageBundle\Manager\ThreadManager
	 */
	public function getThreadManager()
	{
		if (null == $this->threadManager) {
			$this->threadManager = $this->container->get('ccdn_message_message.manager.thread');
		}
		
		return $this->threadManager;
	}
	
	/**
	 *
	 * @access public
	 * @return \CCDNMessage\MessageBundle\Manager\RegistryManager
	 */
	public function getRegistryManager()
	{
		if (null == $this->registryManager) {
			$this->registryManager = $this->container->get('ccdn_message_message.manager.registry');
		}
		
		return $this->registryManager;		
	}
	
	/**
	 *
	 * @access public
	 * @return int
	 */
	public function getMessagesPerPageOnFolders()
	{
		if (null == $this->messagesPerPageOnFolders) {
			$this->messagesPerPageOnFolders = $this->container->getParameter('ccdn_message_message.folder.show.messages_per_page');		
		}
			
		return $this->messagesPerPageOnFolders;
	}
	
	/**
	 *
	 * @access public
	 * @return int
	 */
	public function getQuotaMaxAllowanceForMessages()
	{
		if (null == $this->quotaMaxAllowanceForMessages) {
			$this->quotaMaxAllowanceForMessages = $this->container->getParameter('ccdn_message_message.quotas.max_messages');
		}
			
		return $this->quotaMaxAllowanceForMessages;
	}
}