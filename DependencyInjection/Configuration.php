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
					->end()
				->end()
			->end();
		
		$this->addSEOSection($rootNode);
		$this->addFolderSection($rootNode);
		$this->addMessageSection($rootNode);
		$this->addQuotasSection($rootNode);
		
        return $treeBuilder;
    }
	
	
	
	/**
	 *
	 * @access protected
	 * @param ArrayNodeDefinition $node
	 */
	protected function addSEOSection(ArrayNodeDefinition $node)
	{
		$node
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
			->children()
				->arrayNode('folder')
					->addDefaultsIfNotSet()
					->canBeUnset()
					->children()
						->arrayNode('show')
							->addDefaultsIfNotSet()
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
			->children()
				->arrayNode('message')
					->addDefaultsIfNotSet()
					->canBeUnset()
					->children()
						->arrayNode('show')
							->addDefaultsIfNotSet()
							->children()
								->scalarNode('layout_template')->defaultValue('CCDNComponentCommonBundle:Layout:layout_body_right.html.twig')->end()
								->scalarNode('sent_datetime_format')->defaultValue('d-m-Y - H:i')->end()
								->scalarNode('enable_bbcode')->defaultValue(true)->end()
							->end()
						->end()
						->arrayNode('compose')
							->addDefaultsIfNotSet()
							->children()
								->scalarNode('layout_template')->defaultValue('CCDNComponentCommonBundle:Layout:layout_body_right.html.twig')->end()
								->scalarNode('form_theme')->defaultValue('CCDNMessageMessageBundle:Form:fields.html.twig')->end()								
								->scalarNode('enable_bbcode')->defaultValue(true)->end()
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
			->children()
				->arrayNode('quotas')
					->children()
						->scalarNode('max_messages')->defaultValue('200')->end()
					->end()
				->end()
			->end();
	}
	
	
}
