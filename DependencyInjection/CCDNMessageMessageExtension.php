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
	public function getAlias()
	{
		return 'ccdn_message_message';
	}
	
	
	
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
		
		$this->getSEOSection($container, $config);
		$this->getFolderSection($container, $config);
		$this->getMessageSection($container, $config);
		$this->getQuotasSection($container, $config);
    }
	
	
	
	/**
	 *
	 * @access protected
	 * @param $container, $config
	 */
	protected function getSEOSection($container, $config)
	{
	    $container->setParameter('ccdn_message_message.seo.title_length', $config['seo']['title_length']);
	}
	
	
	
	/**
	 *
	 * @access private
	 * @param $container, $config
	 */
	private function getFolderSection($container, $config)
	{
		$container->setParameter('ccdn_message_message.folder.show.layout_template', $config['folder']['show']['layout_template']);
		$container->setParameter('ccdn_message_message.folder.show.messages_per_page', $config['folder']['show']['messages_per_page']);
		$container->setParameter('ccdn_message_message.folder.show.subject_truncate', $config['folder']['show']['subject_truncate']);
		$container->setParameter('ccdn_message_message.folder.show.sent_datetime_format', $config['folder']['show']['sent_datetime_format']);
	}


	
	/**
	 *
	 * @access private
	 * @param $container, $config
	 */
	private function getMessageSection($container, $config)
	{
		$container->setParameter('ccdn_message_message.message.show.layout_template', $config['message']['show']['layout_template']);
		$container->setParameter('ccdn_message_message.message.show.sent_datetime_format', $config['message']['show']['sent_datetime_format']);
		$container->setParameter('ccdn_message_message.message.show.enable_bb_parser', $config['message']['show']['enable_bb_parser']);

		$container->setParameter('ccdn_message_message.message.compose.layout_template', $config['message']['compose']['layout_template']);
		$container->setParameter('ccdn_message_message.message.compose.form_theme', $config['message']['compose']['form_theme']);
		$container->setParameter('ccdn_message_message.message.compose.enable_bb_editor', $config['message']['compose']['enable_bb_editor']);
	}
	
	
	
	/**
	 *
	 * @access private
	 * @param $container, $config
	 */
	private function getQuotasSection($container, $config)
	{
		$container->setParameter('ccdn_message_message.quotas.max_messages', $config['quotas']['max_messages']);
	}	
	
}
