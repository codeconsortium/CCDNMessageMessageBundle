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
 * @category CCDNMessage
 * @package  MessageBundle
 *
 * @author   Reece Fowell <reece@codeconsortium.com>
 * @license  http://opensource.org/licenses/MIT MIT
 * @version  Release: 2.0
 * @link     https://github.com/codeconsortium/CCDNMessageMessageBundle
 *
 */
class CCDNMessageMessageExtension extends Extension
{
    /**
     *
     * @access public
     * @return string
     */
    public function getAlias()
    {
        return 'ccdn_message_message';
    }

    /**
     *
     * @access public
     * @param array                                                   $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('ccdn_message_message.template.engine', $config['template']['engine']);

        // Class file namespaces.
        $this
            ->getEntitySection($config, $container)
            ->getRepositorySection($config, $container)
            ->getGatewaySection($config, $container)
            ->getManagerSection($config, $container)
            ->getFormSection($config, $container)
            ->getComponentSection($config, $container)
        ;

        // Configuration stuff.
        $this
            ->getQuotasSection($config, $container)
            ->getSEOSection($config, $container)
            ->getFolderSection($config, $container)
            ->getMessageSection($config, $container)
        ;

        // Load Service definitions.
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     *
     * @access private
     * @param  array                                                                      $config
     * @param  \Symfony\Component\DependencyInjection\ContainerBuilder                    $container
     * @return \CCDNMessage\MessageBundle\DependencyInjection\CCDNMessageMessageExtension
     */
    private function getEntitySection(array $config, ContainerBuilder $container)
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

        return $this;
    }

    /**
     *
     * @access private
     * @param  array                                                                      $config
     * @param  \Symfony\Component\DependencyInjection\ContainerBuilder                    $container
     * @return \CCDNMessage\MessageBundle\DependencyInjection\CCDNMessageMessageExtension
     */
    private function getRepositorySection(array $config, ContainerBuilder $container)
    {
        $container->setParameter('ccdn_message_message.repository.folder.class', $config['repository']['folder']['class']);
        $container->setParameter('ccdn_message_message.repository.message.class', $config['repository']['message']['class']);
        $container->setParameter('ccdn_message_message.repository.envelope.class', $config['repository']['envelope']['class']);
        $container->setParameter('ccdn_message_message.repository.registry.class', $config['repository']['registry']['class']);
        $container->setParameter('ccdn_message_message.repository.thread.class', $config['repository']['thread']['class']);

        return $this;
    }

    /**
     *
     * @access private
     * @param  array                                                                      $config
     * @param  \Symfony\Component\DependencyInjection\ContainerBuilder                    $container
     * @return \CCDNMessage\MessageBundle\DependencyInjection\CCDNMessageMessageExtension
     */
    private function getGatewaySection(array $config, ContainerBuilder $container)
    {
        $container->setParameter('ccdn_message_message.gateway_bag.class', $config['gateway']['bag']['class']);

        $container->setParameter('ccdn_message_message.gateway.folder.class', $config['gateway']['folder']['class']);
        $container->setParameter('ccdn_message_message.gateway.message.class', $config['gateway']['message']['class']);
        $container->setParameter('ccdn_message_message.gateway.envelope.class', $config['gateway']['envelope']['class']);
        $container->setParameter('ccdn_message_message.gateway.registry.class', $config['gateway']['registry']['class']);
        $container->setParameter('ccdn_message_message.gateway.thread.class', $config['gateway']['thread']['class']);
        $container->setParameter('ccdn_message_message.gateway.user.class', $config['gateway']['user']['class']);

        return $this;
    }

    /**
     *
     * @access private
     * @param  array                                                                      $config
     * @param  \Symfony\Component\DependencyInjection\ContainerBuilder                    $container
     * @return \CCDNMessage\MessageBundle\DependencyInjection\CCDNMessageMessageExtension
     */
    private function getManagerSection(array $config, ContainerBuilder $container)
    {
        $container->setParameter('ccdn_message_message.manager_bag.class', $config['manager']['bag']['class']);

        $container->setParameter('ccdn_message_message.manager.folder.class', $config['manager']['folder']['class']);
        $container->setParameter('ccdn_message_message.manager.message.class', $config['manager']['message']['class']);
        $container->setParameter('ccdn_message_message.manager.envelope.class', $config['manager']['envelope']['class']);
        $container->setParameter('ccdn_message_message.manager.registry.class', $config['manager']['registry']['class']);
        $container->setParameter('ccdn_message_message.manager.thread.class', $config['manager']['thread']['class']);
        $container->setParameter('ccdn_message_message.manager.user.class', $config['manager']['user']['class']);

        return $this;
    }

    /**
     *
     * @access private
     * @param  array                                                                      $config
     * @param  \Symfony\Component\DependencyInjection\ContainerBuilder                    $container
     * @return \CCDNMessage\MessageBundle\DependencyInjection\CCDNMessageMessageExtension
     */
    private function getFormSection(array $config, ContainerBuilder $container)
    {
        $container->setParameter('ccdn_message_message.form.type.message.class', $config['form']['type']['message']['class']);
        $container->setParameter('ccdn_message_message.form.handler.message.class', $config['form']['handler']['message']['class']);
        $container->setParameter('ccdn_message_message.form.handler.message_reply.class', $config['form']['handler']['message_reply']['class']);
        $container->setParameter('ccdn_message_message.form.handler.message_forward.class', $config['form']['handler']['message_forward']['class']);
        $container->setParameter('ccdn_message_message.form.validator.send_to.class', $config['form']['validator']['send_to']['class']);

        return $this;
    }

    /**
     *
     * @access private
     * @param  array                                                                      $config
     * @param  \Symfony\Component\DependencyInjection\ContainerBuilder                    $container
     * @return \CCDNMessage\MessageBundle\DependencyInjection\CCDNMessageMessageExtension
     */
    private function getComponentSection(array $config, ContainerBuilder $container)
    {
        $container->setParameter('ccdn_message_message.component.dashboard.integrator.class', $config['component']['dashboard']['integrator']['class']);

        $container->setParameter('ccdn_message_message.component.twig_extension.unread_message_count.class', $config['component']['twig_extension']['unread_message_count']['class']);
        $container->setParameter('ccdn_message_message.component.twig_extension.folder_list.class', $config['component']['twig_extension']['folder_list']['class']);

        $container->setParameter('ccdn_message_message.component.flood_control.class', $config['component']['flood_control']['class']);

        return $this;
    }

    /**
     *
     * @access private
     * @param  array                                                                      $config
     * @param  \Symfony\Component\DependencyInjection\ContainerBuilder                    $container
     * @return \CCDNMessage\MessageBundle\DependencyInjection\CCDNMessageMessageExtension
     */
    private function getSEOSection(array $config, ContainerBuilder $container)
    {
        $container->setParameter('ccdn_message_message.seo.title_length', $config['seo']['title_length']);

        return $this;
    }

    /**
     *
     * @access private
     * @param  array                                                                      $config
     * @param  \Symfony\Component\DependencyInjection\ContainerBuilder                    $container
     * @return \CCDNMessage\MessageBundle\DependencyInjection\CCDNMessageMessageExtension
     */
    private function getFolderSection(array $config, ContainerBuilder $container)
    {
        $container->setParameter('ccdn_message_message.folder.show.layout_template', $config['folder']['show']['layout_template']);
        $container->setParameter('ccdn_message_message.folder.show.messages_per_page', $config['folder']['show']['messages_per_page']);
        $container->setParameter('ccdn_message_message.folder.show.subject_truncate', $config['folder']['show']['subject_truncate']);
        $container->setParameter('ccdn_message_message.folder.show.sent_datetime_format', $config['folder']['show']['sent_datetime_format']);

        return $this;
    }

    /**
     *
     * @access private
     * @param  array                                                                      $config
     * @param  \Symfony\Component\DependencyInjection\ContainerBuilder                    $container
     * @return \CCDNMessage\MessageBundle\DependencyInjection\CCDNMessageMessageExtension
     */
    private function getMessageSection(array $config, ContainerBuilder $container)
    {
        $container->setParameter('ccdn_message_message.message.flood_control.send_limit', $config['message']['flood_control']['send_limit']);
        $container->setParameter('ccdn_message_message.message.flood_control.block_for_minutes', $config['message']['flood_control']['block_for_minutes']);

        $container->setParameter('ccdn_message_message.message.show.layout_template', $config['message']['show']['layout_template']);
        $container->setParameter('ccdn_message_message.message.show.sent_datetime_format', $config['message']['show']['sent_datetime_format']);
        $container->setParameter('ccdn_message_message.message.show.enable_bb_parser', $config['message']['show']['enable_bb_parser']);

        $container->setParameter('ccdn_message_message.message.compose.layout_template', $config['message']['compose']['layout_template']);
        $container->setParameter('ccdn_message_message.message.compose.form_theme', $config['message']['compose']['form_theme']);
        $container->setParameter('ccdn_message_message.message.compose.enable_bb_editor', $config['message']['compose']['enable_bb_editor']);

        return $this;
    }

    /**
     *
     * @access private
     * @param  array                                                                      $config
     * @param  \Symfony\Component\DependencyInjection\ContainerBuilder                    $container
     * @return \CCDNMessage\MessageBundle\DependencyInjection\CCDNMessageMessageExtension
     */
    private function getQuotasSection(array $config, ContainerBuilder $container)
    {
        $container->setParameter('ccdn_message_message.quotas.max_messages', $config['quotas']['max_messages']);

        return $this;
    }
}
