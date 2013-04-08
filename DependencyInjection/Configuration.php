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

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ccdn_message_message');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        $rootNode
            ->children()
                ->arrayNode('template')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('engine')->defaultValue('twig')->end()
                    ->end()
                ->end()
            ->end();

		$this->addEntitySection($rootNode);
		$this->addGatewaySection($rootNode);
		$this->addManagerSection($rootNode);
		
        $this->addSEOSection($rootNode);
        $this->addFolderSection($rootNode);
        $this->addMessageSection($rootNode);
        $this->addQuotasSection($rootNode);

        return $treeBuilder;
    }

    /**
     *
     * @access private
     * @param ArrayNodeDefinition $node
     */
    private function addEntitySection(ArrayNodeDefinition $node)
	{
        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('entity')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
				        ->arrayNode('folder')
				            ->addDefaultsIfNotSet()
				            ->canBeUnset()
				            ->children()
								->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Entity\Folder')->end()
							->end()
						->end()
				        ->arrayNode('message')
				            ->addDefaultsIfNotSet()
				            ->canBeUnset()
				            ->children()
								->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Entity\Message')->end()
							->end()
						->end()
				        ->arrayNode('envelope')
				            ->addDefaultsIfNotSet()
				            ->canBeUnset()
				            ->children()
								->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Entity\Envelope')->end()
							->end()
						->end()
				        ->arrayNode('registry')
				            ->addDefaultsIfNotSet()
				            ->canBeUnset()
				            ->children()
								->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Entity\Registry')->end()
							->end()
						->end()
				        ->arrayNode('thread')
				            ->addDefaultsIfNotSet()
				            ->canBeUnset()
				            ->children()
								->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Entity\Thread')->end()
							->end()
						->end()
				        ->arrayNode('user')
				            ->children()
								->scalarNode('class')->end()
							->end()
						->end()
					->end()
				->end()
			->end();
	}
	
    /**
     *
     * @access private
     * @param ArrayNodeDefinition $node
     */
    private function addGatewaySection(ArrayNodeDefinition $node)
    {
        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('gateway')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->arrayNode('bag')
		                    ->addDefaultsIfNotSet()
		                    ->canBeUnset()
                            ->children()
								->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Gateway\Bag\GatewayBag')->end()							
							->end()
						->end()
                        ->arrayNode('message')
		                    ->addDefaultsIfNotSet()
		                    ->canBeUnset()
                            ->children()
								->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Gateway\MessageGateway')->end()							
							->end()
						->end()
                        ->arrayNode('folder')
		                    ->addDefaultsIfNotSet()
		                    ->canBeUnset()
                            ->children()
								->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Gateway\FolderGateway')->end()							
							->end()
						->end()
                        ->arrayNode('envelope')
		                    ->addDefaultsIfNotSet()
		                    ->canBeUnset()
                            ->children()
								->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Gateway\EnvelopeGateway')->end()							
							->end()
						->end()
                        ->arrayNode('registry')
		                    ->addDefaultsIfNotSet()
		                    ->canBeUnset()
                            ->children()
								->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Gateway\RegistryGateway')->end()							
							->end()
						->end()
                        ->arrayNode('thread')
		                    ->addDefaultsIfNotSet()
		                    ->canBeUnset()
                            ->children()
								->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Gateway\ThreadGateway')->end()							
							->end()
						->end()
                        ->arrayNode('user')
		                    ->addDefaultsIfNotSet()
		                    ->canBeUnset()
                            ->children()
								->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Gateway\UserGateway')->end()							
							->end()
						->end()
					->end()
				->end()
			->end();
	}
	
    /**
     *
     * @access private
     * @param ArrayNodeDefinition $node
     */
    private function addManagerSection(ArrayNodeDefinition $node)
    {
        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('manager')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->arrayNode('bag')
		                    ->addDefaultsIfNotSet()
		                    ->canBeUnset()
                            ->children()
								->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Manager\Bag\ManagerBag')->end()							
							->end()
						->end()
                        ->arrayNode('message')
		                    ->addDefaultsIfNotSet()
		                    ->canBeUnset()
                            ->children()
								->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Manager\MessageManager')->end()							
							->end()
						->end()
                        ->arrayNode('folder')
		                    ->addDefaultsIfNotSet()
		                    ->canBeUnset()
                            ->children()
								->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Manager\FolderManager')->end()							
							->end()
						->end()
                        ->arrayNode('envelope')
		                    ->addDefaultsIfNotSet()
		                    ->canBeUnset()
                            ->children()
								->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Manager\EnvelopeManager')->end()							
							->end()
						->end()
                        ->arrayNode('registry')
		                    ->addDefaultsIfNotSet()
		                    ->canBeUnset()
                            ->children()
								->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Manager\RegistryManager')->end()							
							->end()
						->end()
                        ->arrayNode('thread')
		                    ->addDefaultsIfNotSet()
		                    ->canBeUnset()
                            ->children()
								->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Manager\ThreadManager')->end()							
							->end()
						->end()
                        ->arrayNode('user')
		                    ->addDefaultsIfNotSet()
		                    ->canBeUnset()
                            ->children()
								->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Manager\UserManager')->end()							
							->end()
						->end()
					->end()
				->end()
			->end();
	}
	
    /**
     *
     * @access protected
     * @param ArrayNodeDefinition $node
     */
    protected function addSEOSection(ArrayNodeDefinition $node)
    {
        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('seo')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('title_length')->defaultValue('67')->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     *
     * @access private
     * @param ArrayNodeDefinition $node
     */
    private function addFolderSection(ArrayNodeDefinition $node)
    {
        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('folder')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->arrayNode('show')
		                    ->addDefaultsIfNotSet()
		                    ->canBeUnset()
                            ->children()
                                ->scalarNode('layout_template')->defaultValue('CCDNComponentCommonBundle:Layout:layout_body_right.html.twig')->end()
                                ->scalarNode('messages_per_page')->defaultValue('10')->end()
                                ->scalarNode('subject_truncate')->defaultValue('50')->end()
                                ->scalarNode('sent_datetime_format')->defaultValue('d-m-Y - H:i')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     *
     * @access private
     * @param ArrayNodeDefinition $node
     */
    private function addMessageSection(ArrayNodeDefinition $node)
    {
        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('message')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
						->arrayNode('flood_control')
		                    ->addDefaultsIfNotSet()
		                    ->canBeUnset()
							->children()
								->scalarNode('send_limit')->defaultValue(4)->end()
								->scalarNode('block_for_minutes')->defaultValue(1)->end()
							->end()
						->end()
                        ->arrayNode('show')
		                    ->addDefaultsIfNotSet()
		                    ->canBeUnset()
                            ->children()
                                ->scalarNode('layout_template')->defaultValue('CCDNComponentCommonBundle:Layout:layout_body_right.html.twig')->end()
                                ->scalarNode('sent_datetime_format')->defaultValue('d-m-Y - H:i')->end()
                                ->scalarNode('enable_bb_parser')->defaultValue(true)->end()
                            ->end()
                        ->end()
                        ->arrayNode('compose')
		                    ->addDefaultsIfNotSet()
		                    ->canBeUnset()
                            ->children()
                                ->scalarNode('layout_template')->defaultValue('CCDNComponentCommonBundle:Layout:layout_body_right.html.twig')->end()
                                ->scalarNode('form_theme')->defaultValue('CCDNMessageMessageBundle:Form:fields.html.twig')->end()
                                ->scalarNode('enable_bb_editor')->defaultValue(true)->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
	
    /**
     *
     * @access private
     * @param ArrayNodeDefinition $node
     */
    private function addQuotasSection(ArrayNodeDefinition $node)
    {
        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('quotas')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('max_messages')->defaultValue('200')->end()
                    ->end()
                ->end()
            ->end();
    }
}