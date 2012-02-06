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

namespace CCDNMessage\MessageBundle\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;

use CCDNComponent\CommonBundle\Entity\Manager\EntityManagerInterface;

/**
 * 
 * @author Reece Fowell <reece@codeconsortium.com> 
 * @version 1.0
 */
class MessageFormHandler
{
	
	
	/**
	 *
	 * @access protected
	 */
	protected $factory;
	
	
	/**
	 *
	 * @access protected
	 */
	protected $container;
	
	
	/**
	 *
	 * @access protected
	 */
	protected $request;
	
	
	/**
	 *
	 * @access protected
	 */
	protected $manager;
	
	
	/**
	 *
	 * @access protected
	 */
	protected $options;
	
	
	/**
	 *
	 * @access protected
	 */
	protected $form;


	/**
	 *
	 * @access public
	 * @param FormFactory $factory, ContainerInterface $container, EntityManagerInterface $manager
	 */
	public function __construct(FormFactory $factory, ContainerInterface $container, EntityManagerInterface $manager)
	{
		$this->options = array();
		$this->factory = $factory;
		$this->container = $container;
		$this->manager = $manager;

		$this->request = $container->get('request');
	}
	
	
	/**
	 *
	 * @access public
	 * @param Array() $options
	 * @return $this
	 */
	public function setOptions(array $options = null )
	{
		$this->options = $options;
		
		return $this;
	}
	
	
	/**
	 *
	 * @access public
	 * @return bool
	 */
	public function process()
	{			
		$this->getForm();
		
		if ($this->request->getMethod() == 'POST')
		{
			$this->form->bindRequest($this->request);
		
			$formData = $this->form->getData();
	
			$formData->setSentFrom($this->options['sender']);
			$formData->setSentDate(new \DateTime());
			$formData->setCreatedDate(new \DateTime());
			$formData->setIsDraft(false);
			
			if (isset($this->options['action']))
			{
				if ($this->options['action'] == 'forward')
				{				
					$formData->setInResponseTo($this->options['message']);
				}
			}
			
			if ($this->form->isValid())
			{	
				$this->onSuccess($this->form->getData());
				
				return true;				
			}
		}

		return false;
	}
	
	
	/**
	 *
	 * @access public
	 * @return Form
	 */
	public function getForm()
	{
		if ( ! $this->form)
		{
			$messageType = $this->container->get('message.form.type');
			
			$defaultValues = array();
			
			if (isset($this->options['send_to']))
			{
				$defaultValues['send_to'] = $this->options['send_to']->getUsername();
			}
			
			if (isset($this->options['action']))
			{
				if ($this->options['action'] == 'reply')
				{
					$defaultValues['action'] = 'reply';
					$defaultValues['message'] = $this->options['message'];
				}
				if ($this->options['action'] == 'forward')
				{
					$defaultValues['action'] = 'forward';
				}
			}
			
			$messageType->setDefaultValues($defaultValues);
			
			if (isset($this->options['message']) && isset($this->options['action']))
			{
				if ($this->options['action'] == 'forward')
				{
					$this->form = $this->factory->create($messageType, $this->options['message']);
				} else {
					$this->form = $this->factory->create($messageType);
				}
			} else {
				$this->form = $this->factory->create($messageType);
			}
		}
		
		return $this->form;
	}
	
	
	/**
	 *
	 * @access protected
	 * @param $entity
	 * @return MessageManager
	 */
	protected function onSuccess($entity)
    {
		return $this->manager->insert($entity)->flushNow();
    }

}