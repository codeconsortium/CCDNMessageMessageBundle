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

/**
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
class FloodControl
{
	/**
	 *
	 * @access protected
	 * @var $session
	 */
	protected $session;
	
	/**
	 *
	 * @access protected
	 * @var int $sendLimit
	 */
	protected $sendLimit;
	
	/**
	 *
	 * @access protected
	 * @var int $blockForMinutes
	 */
	protected $blockForMinutes;
	
	/**
	 *
	 * @access public
	 * @param $session
	 */
	public function __construct($session, $sendLimit, $blockForMinutes)
	{
		$this->session = $session;
		
		$this->sendLimit = $sendLimit;
		
		$this->blockForMinutes = $blockForMinutes;
		
		if ( ! $this->session->has('flood_control_message_send_count'))
		{
			$this->session->set('flood_control_message_send_count', array());
		}		
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
        $timeLimit = new \DateTime('-' . $this->blockForMinutes . ' minutes');

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

            if (count($freshenedAttempts) > $this->sendLimit)
			{
				return true;
			}
        }		

		return false;
	}
	
}
