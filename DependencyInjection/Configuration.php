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
 * @category CCDNMessage
 * @package  MessageBundle
 *
 * @author   Reece Fowell <reece@codeconsortium.com>
 * @license  http://opensource.org/licenses/MIT MIT
 * @version  Release: 2.0
 * @link     https://github.com/codeconsortium/CCDNMessageMessageBundle
 *
 */
class Configuration implements ConfigurationInterface
{
    /**
     *
     * @access protected
     * @var string $defaultValueLayoutTemplate
     */
    protected $defaultValueLayoutTemplate = 'CCDNMessageMessageBundle::base.html.twig';

    /**
     *
     * @access protected
     * @var string $defaultValueFormTheme
     */
    protected $defaultValueFormTheme = 'CCDNMessageMessageBundle:Common:Form/fields.html.twig';

    /**
     *
     * @access public
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ccdn_message_message');

        $rootNode
            ->children()
                ->arrayNode('template')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('engine')->defaultValue('twig')->end()
                        ->scalarNode('pager_theme')->defaultValue('CCDNMessageMessageBundle:Common:Paginator/twitter_bootstrap.html.twig')->end()
                    ->end()
                ->end()
            ->end();

        // Class file namespaces.
        $this->addEntitySection($rootNode);
        $this->addRepositorySection($rootNode);
        $this->addGatewaySection($rootNode);
        $this->addManagerSection($rootNode);
		$this->addModelSection($rootNode);
        $this->addFormSection($rootNode);
        $this->addComponentSection($rootNode);

        // Configuration stuff.
        $this->addSEOSection($rootNode);
        $this->addFolderSection($rootNode);
        $this->addMessageSection($rootNode);
        $this->addQuotasSection($rootNode);

        return $treeBuilder;
    }

    /**
     *
     * @access private
     * @param  \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     * @return \CCDNMessage\MessageBundle\DependencyInjection\Configuration
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
                        ->arrayNode('envelope')
                            ->addDefaultsIfNotSet()
                            ->canBeUnset()
                            ->children()
                                ->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Entity\Envelope')->end()
                            ->end()
                        ->end()
                        ->arrayNode('message')
                            ->addDefaultsIfNotSet()
                            ->canBeUnset()
                            ->children()
                                ->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Entity\Message')->end()
                            ->end()
                        ->end()
                        ->arrayNode('thread')
                            ->addDefaultsIfNotSet()
                            ->canBeUnset()
                            ->children()
                                ->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Entity\Thread')->end()
                            ->end()
                        ->end()
                        ->arrayNode('registry')
                            ->addDefaultsIfNotSet()
                            ->canBeUnset()
                            ->children()
                                ->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Entity\Registry')->end()
                            ->end()
                        ->end()
                        ->arrayNode('user')
                            ->children()
                                ->scalarNode('class')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $this;
    }

    /**
     *
     * @access private
     * @param  \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     * @return \CCDNMessage\MessageBundle\DependencyInjection\Configuration
     */
    private function addRepositorySection(ArrayNodeDefinition $node)
    {
        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('repository')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->arrayNode('folder')
                            ->addDefaultsIfNotSet()
                            ->canBeUnset()
                            ->children()
                                ->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Model\Repository\FolderRepository')->end()
                            ->end()
                        ->end()
                        ->arrayNode('envelope')
                            ->addDefaultsIfNotSet()
                            ->canBeUnset()
                            ->children()
                                ->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Model\Repository\EnvelopeRepository')->end()
                            ->end()
                        ->end()
                        ->arrayNode('message')
                            ->addDefaultsIfNotSet()
                            ->canBeUnset()
                            ->children()
                                ->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Model\Repository\MessageRepository')->end()
                            ->end()
                        ->end()
                        ->arrayNode('registry')
                            ->addDefaultsIfNotSet()
                            ->canBeUnset()
                            ->children()
                                ->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Model\Repository\RegistryRepository')->end()
                            ->end()
                        ->end()
                        ->arrayNode('thread')
                            ->addDefaultsIfNotSet()
                            ->canBeUnset()
                            ->children()
                                ->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Model\Repository\ThreadRepository')->end()
                            ->end()
                        ->end()
                        ->arrayNode('user')
                            ->addDefaultsIfNotSet()
                            ->canBeUnset()
                            ->children()
                                ->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Model\Repository\UserRepository')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $this;
    }

    /**
     *
     * @access private
     * @param  \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     * @return \CCDNMessage\MessageBundle\DependencyInjection\Configuration
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
	                    ->arrayNode('folder')
	                        ->addDefaultsIfNotSet()
	                        ->canBeUnset()
	                        ->children()
	                            ->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Model\Gateway\FolderGateway')->end()
	                        ->end()
	                    ->end()
	                    ->arrayNode('envelope')
	                        ->addDefaultsIfNotSet()
	                        ->canBeUnset()
	                        ->children()
	                            ->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Model\Gateway\EnvelopeGateway')->end()
	                        ->end()
	                    ->end()
                        ->arrayNode('message')
                            ->addDefaultsIfNotSet()
                            ->canBeUnset()
                            ->children()
                                ->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Model\Gateway\MessageGateway')->end()
                            ->end()
                        ->end()
                        ->arrayNode('thread')
                            ->addDefaultsIfNotSet()
                            ->canBeUnset()
                            ->children()
                                ->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Model\Gateway\ThreadGateway')->end()
                            ->end()
                        ->end()
                        ->arrayNode('registry')
                            ->addDefaultsIfNotSet()
                            ->canBeUnset()
                            ->children()
                                ->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Model\Gateway\RegistryGateway')->end()
                            ->end()
                        ->end()
                        ->arrayNode('user')
                            ->addDefaultsIfNotSet()
                            ->canBeUnset()
                            ->children()
                                ->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Model\Gateway\UserGateway')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $this;
    }

    /**
     *
     * @access private
     * @param  \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     * @return \CCDNMessage\MessageBundle\DependencyInjection\Configuration
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
	                    ->arrayNode('folder')
	                        ->addDefaultsIfNotSet()
	                        ->canBeUnset()
	                        ->children()
	                            ->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Model\Manager\FolderManager')->end()
	                        ->end()
	                    ->end()
	                    ->arrayNode('envelope')
	                        ->addDefaultsIfNotSet()
	                        ->canBeUnset()
	                        ->children()
	                            ->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Model\Manager\EnvelopeManager')->end()
	                        ->end()
	                    ->end()
                        ->arrayNode('message')
                            ->addDefaultsIfNotSet()
                            ->canBeUnset()
                            ->children()
                                ->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Model\Manager\MessageManager')->end()
                            ->end()
                        ->end()
                        ->arrayNode('thread')
                            ->addDefaultsIfNotSet()
                            ->canBeUnset()
                            ->children()
                                ->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Model\Manager\ThreadManager')->end()
                            ->end()
                        ->end()
                        ->arrayNode('registry')
                            ->addDefaultsIfNotSet()
                            ->canBeUnset()
                            ->children()
                                ->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Model\Manager\RegistryManager')->end()
                            ->end()
                        ->end()
                        ->arrayNode('user')
                            ->addDefaultsIfNotSet()
                            ->canBeUnset()
                            ->children()
                                ->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Model\Manager\UserManager')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $this;
    }

    /**
     *
     * @access private
     * @param  \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     * @return \CCDNMessage\MessageBundle\DependencyInjection\Configuration
     */
    private function addModelSection(ArrayNodeDefinition $node)
    {
        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('model')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->arrayNode('folder')
                            ->addDefaultsIfNotSet()
                            ->canBeUnset()
                            ->children()
                                ->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Model\Model\FolderModel')->end()
                            ->end()
                        ->end()
                        ->arrayNode('envelope')
                            ->addDefaultsIfNotSet()
                            ->canBeUnset()
                            ->children()
                                ->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Model\Model\EnvelopeModel')->end()
                            ->end()
                        ->end()
                        ->arrayNode('message')
                            ->addDefaultsIfNotSet()
                            ->canBeUnset()
                            ->children()
                                ->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Model\Model\MessageModel')->end()
                            ->end()
                        ->end()
                        ->arrayNode('thread')
                            ->addDefaultsIfNotSet()
                            ->canBeUnset()
                            ->children()
                                ->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Model\Model\ThreadModel')->end()
                            ->end()
                        ->end()
                        ->arrayNode('registry')
                            ->addDefaultsIfNotSet()
                            ->canBeUnset()
                            ->children()
                                ->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Model\Model\RegistryModel')->end()
                            ->end()
                        ->end()
                        ->arrayNode('user')
                            ->addDefaultsIfNotSet()
                            ->canBeUnset()
                            ->children()
                                ->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Model\Model\UserModel')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $this;
    }

    /**
     *
     * @access private
     * @param  \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     * @return \CCDNMessage\MessageBundle\DependencyInjection\Configuration
     */
    private function addFormSection(ArrayNodeDefinition $node)
    {
        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('form')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->arrayNode('type')
                            ->addDefaultsIfNotSet()
                            ->canBeUnset()
                            ->children()
                                ->arrayNode('message')
                                    ->addDefaultsIfNotSet()
                                    ->canBeUnset()
                                    ->children()
                                        ->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Form\Type\User\MessageFormType')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('handler')
                            ->addDefaultsIfNotSet()
                            ->canBeUnset()
                            ->children()
                                ->arrayNode('message')
                                    ->addDefaultsIfNotSet()
                                    ->canBeUnset()
                                    ->children()
                                        ->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Form\Handler\User\MessageFormHandler')->end()
                                    ->end()
                                ->end()
                                ->arrayNode('message_reply')
                                    ->addDefaultsIfNotSet()
                                    ->canBeUnset()
                                    ->children()
                                        ->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Form\Handler\User\MessageReplyFormHandler')->end()
                                    ->end()
                                ->end()
                                ->arrayNode('message_forward')
                                    ->addDefaultsIfNotSet()
                                    ->canBeUnset()
                                    ->children()
                                        ->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Form\Handler\User\MessageForwardFormHandler')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('validator')
                            ->addDefaultsIfNotSet()
                            ->canBeUnset()
                            ->children()
                                ->arrayNode('send_to')
                                    ->addDefaultsIfNotSet()
                                    ->canBeUnset()
                                    ->children()
                                        ->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Form\Validator\SendToValidator')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $this;
    }

    /**
     *
     * @access private
     * @param  \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     * @return \CCDNMessage\MessageBundle\DependencyInjection\Configuration
     */
    private function addComponentSection(ArrayNodeDefinition $node)
    {
        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('component')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->arrayNode('dashboard')
                            ->addDefaultsIfNotSet()
                            ->canBeUnset()
                            ->children()
                                ->arrayNode('integrator')
                                    ->addDefaultsIfNotSet()
                                    ->canBeUnset()
                                    ->children()
                                        ->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Component\Dashboard\DashboardIntegrator')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('twig_extension')
                            ->addDefaultsIfNotSet()
                            ->canBeUnset()
                            ->children()
                                ->arrayNode('unread_message_count')
                                    ->addDefaultsIfNotSet()
                                    ->canBeUnset()
                                    ->children()
                                        ->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Component\TwigExtension\UnreadMessageCountExtension')->end()
                                    ->end()
                                ->end()
                                ->arrayNode('folder_list')
                                    ->addDefaultsIfNotSet()
                                    ->canBeUnset()
                                    ->children()
                                        ->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Component\TwigExtension\FolderListExtension')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('flood_control')
                            ->addDefaultsIfNotSet()
                            ->canBeUnset()
                            ->children()
                                ->scalarNode('class')->defaultValue('CCDNMessage\MessageBundle\Component\FloodControl')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $this;
    }

    /**
     *
     * @access protected
     * @param  \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     * @return \CCDNMessage\MessageBundle\DependencyInjection\Configuration
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
            ->end()
        ;

        return $this;
    }

    /**
     *
     * @access private
     * @param  \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     * @return \CCDNMessage\MessageBundle\DependencyInjection\Configuration
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
                                ->scalarNode('layout_template')->defaultValue($this->defaultValueLayoutTemplate)->end()
                                ->scalarNode('messages_per_page')->defaultValue('10')->end()
                                ->scalarNode('subject_truncate')->defaultValue('50')->end()
                                ->scalarNode('sent_datetime_format')->defaultValue('d-m-Y - H:i')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $this;
    }

    /**
     *
     * @access private
     * @param  \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     * @return \CCDNMessage\MessageBundle\DependencyInjection\Configuration
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
                                ->scalarNode('layout_template')->defaultValue($this->defaultValueLayoutTemplate)->end()
                                ->scalarNode('sent_datetime_format')->defaultValue('d-m-Y - H:i')->end()
                            ->end()
                        ->end()
                        ->arrayNode('compose')
                            ->addDefaultsIfNotSet()
                            ->canBeUnset()
                            ->children()
                                ->scalarNode('layout_template')->defaultValue($this->defaultValueLayoutTemplate)->end()
                                ->scalarNode('form_theme')->defaultValue($this->defaultValueFormTheme)->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $this;
    }

    /**
     *
     * @access private
     * @param  \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     * @return \CCDNMessage\MessageBundle\DependencyInjection\Configuration
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
            ->end()
        ;

        return $this;
    }
}
