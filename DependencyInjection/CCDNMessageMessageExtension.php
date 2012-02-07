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

namespace CCDNMessage\MessageBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 * 
 * @author Reece Fowell <reece@codeconsortium.com> 
 * @version 1.0
 */
class CCDNMessageMessageExtension extends Extension
{
	
	
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');


		$container->setParameter('ccdn_message_message.template.engine', $config['template']['engine']);
		$container->setParameter('ccdn_message_message.user.profile_route', $config['user']['profile_route']);
		
		$this->getFolderSection($container, $config);
		$this->getMessageSection($container, $config);
    }
	
	
	/**
	 *
	 * @access private
	 * @param $container, $config
	 */
	private function getFolderSection($container, $config)
	{
		$container->setParameter('ccdn_message_message.folder.messages_per_page', $config['folder']['messages_per_page']);
	}

	
	/**
	 *
	 * @access private
	 * @param $container, $config
	 */
	private function getMessageSection($container, $config)
	{

	}
	
}
