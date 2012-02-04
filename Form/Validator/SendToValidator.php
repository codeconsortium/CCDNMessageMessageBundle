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

namespace CCDNMessage\MessageBundle\Form\Validator;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

/**
 * 
 * @author Reece Fowell <reece@codeconsortium.com> 
 * @version 1.0
 */
class SendToValidator extends ConstraintValidator
{
	
	
	/**
	 *
	 * @access protected
	 */
	protected $doctrine;
	
	
	/**
	 *
	 * @access protected
	 */
	protected $container;


	/**
	 *
	 * @access public
	 * @param $doctrine, $service_container
	 */
	public function __construct($doctrine, $service_container)
	{
		
		$this->doctrine = $doctrine;
		$this->container = $service_container;
	}
	
	
	/**
	 *
	 * @access public
	 * @param $value, Constraint $constraint
	 * @return bool
	 */
	public function isValid($value, Constraint $constraint)
	{
		// convert either one user or mulitple users who
		// the mail will be sent to into user entities.	
		if ($recipients = preg_split('/((,)|(\s))/', $value, PREG_OFFSET_CAPTURE))
		{
			foreach ($recipients as $key => $recipient)
			{			
				$recipients[$key] = preg_replace("/[^a-zA-Z0-9_]/", "", $recipients[$key]);

				if ( ! $recipient)
				{
					unset($recipients[$key]);
				}
			}				

			$sendToUsers = $this->container->get('user.repository')->findTheseUsersByUsername($recipients);				
		} else {
			$recipients = array($value);
			
			$sendToUsers = $this->container->get('user.repository')->findByUsername($recipients);
		}
		
		$notFound = array();
		foreach ($recipients as $recipientKey => $recipient)
		{
			$recipientsFound = 0;

			foreach ($sendToUsers as $sendToUserkey => $sendToUser)
			{
				if ($sendToUser->getUsername() == $recipient)
				{
					$recipientsFound++;
				}
			}
			
			if ($recipientsFound == 0)
			{
				$notFound[] = $recipient;
			}
		}
		
		if (count($notFound) > 0)
		{
			$constraint->addNotFoundUsernames($notFound);
			
			$this->setMessage($constraint->message);

			return false;
		} else {
			return true;
		}
	}
	
}