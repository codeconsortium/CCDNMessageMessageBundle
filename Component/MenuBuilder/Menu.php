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

namespace CCDNMessage\MessageBundle\Component\MenuBuilder;

/**
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
class Menu
{

	private $container;
	
	public function __construct($container)
	{
		$this->container = $container;
	}
	
    /**
     *      
	 * @access public
	 * @return array
     */
    public function buildMenu($builder)
    {
		$container = $this->container;
		
		$callbackLabel = function() use($container)
		{
			if ($container->get('security.context')->isGranted('ROLE_USER'))
			{
			    $user = $container->get('security.context')->getToken()->getUser();

		        $unreadMessageCountObj = $container->get('ccdn_message_message.repository.registry')->findRegistryRecordForUser($user->getId());

		        if ($unreadMessageCountObj == null) {
		            $unreadMessageCount = ' 0';
		        } else {
			        $unreadMessageCount = $unreadMessageCountObj->getCachedUnreadMessagesCount();
			
					if ($unreadMessageCount == null) { $unreadMessageCount = ' 0'; }
				}				
			} else {
				$unreadMessageCount = ' 0';
			}
			
			$container->get('session')->set('message_count', $unreadMessageCount);
			
			return $unreadMessageCount;
		};
		
		$callbackTitle = function() use($container)
		{
			$unreadMessageCount = $container->get('session')->get('message_count');
			
			$messageButtonTitle = $container->get('translator')->trans('ccdn_message_message.message.inbox_status', array('%message_count%'=> $unreadMessageCount), 'CCDNMessageMessageBundle');
			
			return $messageButtonTitle;
		};
		
		$callbackHtmlClass = function() use($container)
		{
			$unreadMessageCount = $container->get('session')->get('message_count');
			
			$class = 'btn' . (($unreadMessageCount > 0) ? ' btn-danger': '');
			
			return $class;
		};
		
		$callbackHtmlStyle = function() use($container)
		{
			$unreadMessageCount = $container->get('session')->get('message_count');
			
			$style = 'vertical-align:middle; ' . (($unreadMessageCount > 0) ? ' font-weight:bold;': '');
			
			return $style;		
		};
		
		$builder
			->arrayNode('layout')
				->arrayNode('header')
					->arrayNode('bottom')
						->linkNode('Messages', 'ccdn_message_message_index', array(
						//	'route_params' => array(),
							'auth' => 'ROLE_USER',
							'class' => $callbackHtmlClass,
							'style' => $callbackHtmlStyle,
							'rel' => 'nofollow',
							'data_attributes' => array(
								'tip' => 'bottom',
								'original-title' => $callbackTitle,
							),
							//'title' => $callbackTitle,
							//'title_trans_params => array(),
							//'title_trans_bundle' => 'CCDNMessageMessageBundle',
							'label' => $callbackLabel,
							'label_trans_params' => array(),
							'label_trans_bundle' => 'CCDNMessageMessageBundle',
						))->end()
					->end()
				->end()
			->end();	
    }

}
