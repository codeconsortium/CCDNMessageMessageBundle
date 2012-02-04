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

namespace CCDNMessage\MessageBundle\Form\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
/**
 * 
 * @author Reece Fowell <reece@codeconsortium.com> 
 * @version 1.0
 */
class SendTo extends Constraint
{


	/**
	 *
	 * @access public
	 */
	public $message = 'The users "%users%" were not found.';


	/**
	 *
	 * @access public
	 * @param Array() $usernames
	 */
	public function addNotFoundUsernames($usernames)
	{
		$usernames = implode(", ", $usernames);
		$this->message = str_replace("%users%", $usernames, $this->message);
	}
	
	
	/*
	 *
	 * @access public
	 * @return string
	 */
	public function validatedBy()
	{
		return 'send_to';
	}
	
}