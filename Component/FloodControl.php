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

namespace CCDNMessage\MessageBundle\Component;

use Symfony\Component\DependencyInjection\ContainerAware;

/**
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
class FloodControl extends ContainerAware
{

	/**
	 *
	 * @access protected
	 */
	protected $session;
	
	/**
	 *
	 * @access protected
	 */
	protected $container;
	
	/**
	 *
	 * @access public
	 * @param $session
	 */
	public function __construct($session, $container)
	{
		$this->session = $session;
		
		if ( ! $this->session->has('flood_control_message_send_count'))
		{
			$this->session->set('flood_control_message_send_count', array());
		}
		
		$this->container = $container;
	}
	
	/**
	 *
	 * @access public
	 */
	public function incrementCounter()
	{
		$sendCount = $this->session->get('flood_control_message_send_count');
		
		$sendCount[] = new \DateTime('now');
		
		$this->session->set('flood_control_message_send_count', $sendCount);
	}
	
	/**
	 *
	 * @access public
	 * @return bool
	 */
	public function isFlooded()
	{
        $blockInMinutes = $this->container->getParameter('ccdn_message_message.message.flood_control.block_for_minutes');

        $timeLimit = new \DateTime('-' . $blockInMinutes . ' minutes');

        if ($this->session->has('flood_control_message_send_count')) {
            $attempts = $this->session->get('flood_control_message_send_count');

            // Iterate over attempts and only reveal attempts that fall within the $timeLimit.
            $freshenedAttempts = array();

            $limit = $timeLimit->getTimestamp();

            foreach ($attempts as $attempt) {
                $date = $attempt->getTimestamp();

                if ($date > $limit) {
                    $freshenedAttempts[] = $attempt;
                }
            }

            if (count($freshenedAttempts) > $this->container->getParameter('ccdn_message_message.message.flood_control.send_limit'))
			{
				return true;
			}
        }		

		return false;
	}
	
}
