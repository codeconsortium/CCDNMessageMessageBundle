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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
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
				->arrayNode('user')
					->children()
						->scalarNode('profile_route')->defaultValue('cc_profile_show_by_id')->end()
					->end()
				->end()
				->arrayNode('template')
					->children()
						->scalarNode('engine')->defaultValue('twig')->end()
						->scalarNode('theme')->defaultValue('CCDNMessageMessageBundle:Form:fields.html.twig')->end()
					->end()
				->end()
				->arrayNode('folder')
					->children()
						->scalarNode('messages_per_page')->defaultValue('40')->end()
					->end()
				->end()
			->end();
		
		$this->addFolderSection($rootNode);
		$this->addMessageSection($rootNode);
		
        return $treeBuilder;
    }


	/**
	 *
	 * @access private
	 * @param ArrayNodeDefinition $node
	 */
	private function addFolderSection(ArrayNodeDefinition $node)
	{
		$node
			->children()
				->arrayNode('folder')
					->children()
						->scalarNode('messages_per_page')->defaultValue('40')->end()
						->arrayNode('layout_templates')
							->children()
								->scalarNode('show')->defaultValue('CCDNComponentCommonBundle:Layout:layout_body_left.html.twig')->end()
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
			->children()
				->arrayNode('message')
					->children()
						->arrayNode('layout_templates')
							->children()
								->scalarNode('compose')->defaultValue('CCDNComponentCommonBundle:Layout:layout_body_left.html.twig')->end()
								->scalarNode('show')->defaultValue('CCDNComponentCommonBundle:Layout:layout_body_left.html.twig')->end()
							->end()
						->end()
					->end()
				->end()
			->end();
	}
	
}
