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

namespace CCDNMessage\MessageBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
class CCDNMessageMessageBundle extends Bundle
{
		
	/**
	 *
	 * @access public
	 */
	public function boot()
	{
		$twig = $this->container->get('twig');	
		$twig->addGlobal('ccdn_message_message', array(
			'seo' => array(
				'title_length' => $this->container->getParameter('ccdn_message_message.seo.title_length'),
			),
			'folder' => array(
				'show' => array(
					'layout_template' => $this->container->getParameter('ccdn_message_message.folder.show.layout_template'),
					'subject_truncate' => $this->container->getParameter('ccdn_message_message.folder.show.subject_truncate'),
					'sent_datetime_format' => $this->container->getParameter('ccdn_message_message.folder.show.sent_datetime_format'),
				),
			),
			'message' => array(
				'show' => array(
					'layout_template' => $this->container->getParameter('ccdn_message_message.message.show.layout_template'),
					'sent_datetime_format' => $this->container->getParameter('ccdn_message_message.message.show.sent_datetime_format'),
					'enable_bb_parser' => $this->container->getParameter('ccdn_message_message.message.show.enable_bb_parser'),
				),
				'compose' => array(
					'layout_template' => $this->container->getParameter('ccdn_message_message.message.compose.layout_template'),
					'form_theme' => $this->container->getParameter('ccdn_message_message.message.compose.form_theme'),
					'enable_bb_editor' => $this->container->getParameter('ccdn_message_message.message.compose.enable_bb_editor'),
				),
			),
		));
	}
}
