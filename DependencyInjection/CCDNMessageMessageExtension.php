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

namespace CCDNMessage\MessageBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\FileLocator;

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

        $container->setParameter('ccdn_message_message.template.engine', $config['template']['engine']);

		// Class file namespaces.
        $this->getEntitySection($container, $config);
        $this->getGatewaySection($container, $config);
        $this->getManagerSection($container, $config);
		
		// Configuration stuff.
        $this->getQuotasSection($container, $config);
        $this->getSEOSection($container, $config);
        $this->getFolderSection($container, $config);
        $this->getMessageSection($container, $config);

		// Load Service definitions.
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
	
    /**
     *
     * @access private
     * @param $container, $config
     */
    private function getEntitySection($container, $config)
    {
        $container->setParameter('ccdn_message_message.entity.folder.class', $config['entity']['folder']['class']);
        $container->setParameter('ccdn_message_message.entity.message.class', $config['entity']['message']['class']);
        $container->setParameter('ccdn_message_message.entity.envelope.class', $config['entity']['envelope']['class']);
        $container->setParameter('ccdn_message_message.entity.registry.class', $config['entity']['registry']['class']);
        $container->setParameter('ccdn_message_message.entity.thread.class', $config['entity']['thread']['class']);
		
		if (! array_key_exists('class', $config['entity']['user'])) {
			throw new \Exception('You must set the class of the User entity in "app/config/config.yml" or some imported configuration file.');
		}

        $container->setParameter('ccdn_message_message.entity.user.class', $config['entity']['user']['class']);				
	}
	
    /**
     *
     * @access private
     * @param $container, $config
     */
    private function getGatewaySection($container, $config)
    {
        $container->setParameter('ccdn_message_message.gateway_bag.class', $config['gateway']['bag']['class']);

        $container->setParameter('ccdn_message_message.gateway.folder.class', $config['gateway']['folder']['class']);
        $container->setParameter('ccdn_message_message.gateway.message.class', $config['gateway']['message']['class']);
        $container->setParameter('ccdn_message_message.gateway.envelope.class', $config['gateway']['envelope']['class']);
        $container->setParameter('ccdn_message_message.gateway.registry.class', $config['gateway']['registry']['class']);
        $container->setParameter('ccdn_message_message.gateway.thread.class', $config['gateway']['thread']['class']);
        $container->setParameter('ccdn_message_message.gateway.user.class', $config['gateway']['user']['class']);
	}
	
    /**
     *
     * @access private
     * @param $container, $config
     */
    private function getManagerSection($container, $config)
    {
        $container->setParameter('ccdn_message_message.manager_bag.class', $config['manager']['bag']['class']);

        $container->setParameter('ccdn_message_message.manager.folder.class', $config['manager']['folder']['class']);
        $container->setParameter('ccdn_message_message.manager.message.class', $config['manager']['message']['class']);
        $container->setParameter('ccdn_message_message.manager.envelope.class', $config['manager']['envelope']['class']);
        $container->setParameter('ccdn_message_message.manager.registry.class', $config['manager']['registry']['class']);
        $container->setParameter('ccdn_message_message.manager.thread.class', $config['manager']['thread']['class']);
        $container->setParameter('ccdn_message_message.manager.user.class', $config['manager']['user']['class']);		
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
        $container->setParameter('ccdn_message_message.message.flood_control.send_limit', $config['message']['flood_control']['send_limit']);
        $container->setParameter('ccdn_message_message.message.flood_control.block_for_minutes', $config['message']['flood_control']['block_for_minutes']);

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