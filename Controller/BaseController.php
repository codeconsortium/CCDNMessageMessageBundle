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

namespace CCDNMessage\MessageBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
class BaseController extends ContainerAware
{
	/**
	 *
	 * @var \Symfony\Bundle\FrameworkBundle\Translation\Translator $translator
	 */
	private $translator;

	/**
	 *
	 * @var \Symfony\Bundle\FrameworkBundle\Routing\Router $router
	 */
	private $router;

	/**
	 *
	 * @var \Symfony\Bundle\TwigBundle\Debug\TimedTwigEngine $templating
	 */	
	private $templating;
	
	/**
	 *
	 * @var \Symfony\Component\HttpFoundation\Request $request
	 */
	protected $request;
	
	/**
	 *
	 * @var \Symfony\Component\Security\Core\SecurityContext $securityContext
	 */
	private $securityContext;
	
	/**
	 *
	 * @var \CCDNMessage\MessageBundle\Manager\FolderManager $folderManager
	 */
	private $folderManager;

	/**
	 *
	 * @var \CCDNMessage\MessageBundle\Manager\EnvelopeManager $envelopeManager;
	 */
	private $envelopeManager;
	
	/**
	 *
	 * @var \CCDNMessage\MessageBundle\Manager\MessageManager $messageManager
	 */
	private $messageManager;
	
	/** 
	 *
	 * @var \CCDNMessage\MessageBundle\Manager\ThreadManager $threadManager;
	 */
	private $threadManager;
		
	/** 
	 * 
	 * @var \CCDNMessage\MessageBundle\Manager\RegistryManager $registryManager;
	 */
	private $registryManager;
	
	/**
	 *
	 * @access protected
	 * @var \CCDNMessage\MessageBundle\Manager\UserManager $userManager
	 */
	protected $userManager;
		
	/** 
	 * 
	 * @var \CCDNMessage\MessageBundle\Component\FloodControl $floodControl;
	 */
	private $floodControl;
		
	/**
	 *
	 * @access protected
	 * @return \Symfony\Bundle\FrameworkBundle\Translation\Translator
	 */
	protected function getTranslator()
	{
		if (null == $this->translator) {
			$this->translator = $this->container->get('translator');
		}
		
		return $this->translator;
	}
	
	/**
	 *
	 * @access protected
	 * @param string $message
	 * @param Array $params
	 * @param string $bundle
	 * @return string
	 */
	protected function trans($message, $params = array(), $bundle = 'CCDNMessageMessageBundle')
	{
		return $this->getTranslator()->trans($message, $params, $bundle);
	}
	
    /**
     *
     * @access protected
	 * @param string $action, string $value
     * @return string
     */
    protected function setFlash($action, $value)
    {
        $this->container->get('session')->setFlash($action, $value);
    }
	
	/**
	 *
	 * @access protected
	 * @return \Symfony\Bundle\FrameworkBundle\Routing\Router
	 */
	protected function getRouter()
	{
		if (null == $this->router) {
			$this->router = $this->container->get('router');
		}
		
		return $this->router;
	}
	
	/**
	 *
	 * @access protected
	 * @param string $route
	 * @param Array $params
	 * @return string
	 */
	protected function path($route, $params = array())
	{
		return $this->getRouter()->generate($route, $params);
	}
	
	/**
	 *
	 * @access protected
	 * @return \Symfony\Bundle\TwigBundle\Debug\TimedTwigEngine
	 */
	protected function getTemplating()
	{
		if (null == $this->templating) {
			$this->templating = $this->container->get('templating');
		}
		
		return $this->templating;
	}

	/**
	 *
	 * @access protected
	 * @return \Symfony\Component\HttpFoundation\Request
	 */	
	protected function getRequest()
	{
		if (null == $this->request) {
			$this->request = $this->container->get('request');
		}

		return $this->request;
	}
	
	/**
	 *
	 * @access protected
	 * @param string $prefix
	 * @return Array
	 */
	protected function getCheckedItemIds($prefix = 'check_', $enforceNumericType = true)
	{
		$request = $this->getRequest();
		
		$sanitarisedIds = array();
		
		if ($request->request->has($prefix)) {
			$itemIds = $request->request->get($prefix);
			
			foreach($itemIds as $id => $val) {
				if ($enforceNumericType == true) {
					if (! is_numeric($id)) {
						continue;
					}
				}
				
				$sanitarisedIds[] = $id;
			}
		}
		
		return $sanitarisedIds;
	}
	
	/**
	 *
	 * @access protected
	 * @param string $template
	 * @param Array $params
	 * @param string $engine
	 * @return string
	 */
	protected function renderResponse($template, $params = array(), $engine = null)
	{
		return $this->getTemplating()->renderResponse($template . ($engine ?: $this->getEngine()), $params);
	}
	
	/**
	 *
	 * @access protected
	 * @param string $url
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	protected function redirectResponse($url)
	{
		return new RedirectResponse($url);
	}
	
    /**
     *
     * @access protected
     * @return string
     */
    protected function getEngine()
    {
        return $this->container->getParameter('ccdn_message_message.template.engine');
    }
	
	/** 
	 * 
	 * @access protected
	 * @return \Symfony\Component\Security\Core\SecurityContext
	 */
	protected function getSecurityContext()
	{
		if (null == $this->securityContext) {
			$this->securityContext = $this->container->get('security.context');
		}
		
		return $this->securityContext;
	}

	/** 
	 * 
	 * @access protected
	 * @return \Symfony\Component\Security\Core\User\UserInterface
	 */	
	protected function getUser()
	{
		return $this->getSecurityContext()->getToken()->getUser();		
	}
	
	/** 
	 * 
	 * @access protected
	 * @param string $role
	 * @return bool
	 */
	protected function isGranted($role)
	{
		if (! $this->getSecurityContext()->isGranted($role)) {
			return false;
		}
		
		return true;
	}
		
	/** 
	 * 
	 * @access protected
	 * @param string $role|boolean $role
	 * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
	 */
	protected function isAuthorised($role)
	{
		if (is_bool($role)) {
			if ($role == false) {
				throw new AccessDeniedException('You do not have permission to use this resource.');
			}
			
			return true;
		}
		
		if (! $this->isGranted($role)) {
			throw new AccessDeniedException('You do not have permission to use this resource.');
		}
		
		return true;
	}
	
	/**
	 *
	 * @access protected
	 * @param \Object $item
	 * @param string $message
	 * @return bool
	 * @throws Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 */
	protected function isFound($item, $message = null)
	{
		if (null == $item) {
			throw new NotFoundHttpException($message ?: 'Page you are looking for could not be found!');
		}
		
		return true;
	}

	/**
	 *
	 * @access public
	 * @return string
	 */
	public function getSubmitAction()
	{
		$request = $this->getRequest();
		
		if ($request->request->has('submit')) {
			$action = key($request->request->get('submit'));
		} else {
			$action = 'post';
		}
		
		return $action;
	}
	
	/**
	 *
	 * @access protected
	 * @return \CCDNMessage\MessageBundle\Manager\FolderManager
	 */
	protected function getFolderManager()
	{
		if (null == $this->folderManager) {
			$this->folderManager = $this->container->get('ccdn_message_message.manager.folder');
		}
		
		return $this->folderManager;
	}
	
	/**
	 *
	 * @access protected
	 * @return \CCDNMessage\MessageBundle\Manager\EnvelopeManager
	 */
	protected function getEnvelopeManager()
	{
		if (null == $this->envelopeManager) {
			$this->envelopeManager = $this->container->get('ccdn_message_message.manager.envelope');
		}
		
		return $this->envelopeManager;
	}
	
	/**
	 *
	 * @access protected
	 * @return \CCDNMessage\MessageBundle\Manager\MessageManager
	 */
	protected function getMessageManager()
	{
		if (null == $this->messageManager) {
			$this->messageManager = $this->container->get('ccdn_message_message.manager.message');
		}
		
		return $this->messageManager;
	}
	
	/**
	 *
	 * @access protected
	 * @return \CCDNMessage\MessageBundle\Manager\ThreadManager
	 */
	protected function getThreadManager()
	{
		if (null == $this->threadManager) {
			$this->threadManager = $this->container->get('ccdn_message_message.manager.thread');
		}
		
		return $this->threadManager;
	}
	
	/**
	 *
	 * @access protected
	 * @return \CCDNMessage\MessageBundle\Manager\RegistryManager
	 */
	protected function getRegistryManager()
	{
		if (null == $this->registryManager) {
			$this->registryManager = $this->container->get('ccdn_message_message.manager.registry');
		}
		
		return $this->registryManager;
	}
	
	/**
	 *
	 * @access public
	 * @return \CCDNMessage\MessageBundle\Manager\UserManager
	 */
	public function getUserManager()
	{
		if (null == $this->userManager) {
			$this->userManager = $this->container->get('ccdn_message_message.manager.user');
		}
	
		return $this->userManager;
	}
	
	/**
	 *
	 * @access protected
	 * @return \CCDNMessage\MessageBundle\Component\FloodControl
	 */
	protected function getFloodControl()
	{
		if (null == $this->floodControl) {
			$this->floodControl = $this->container->get('ccdn_message_message.component.flood_control');			
		}
		
		return $this->floodControl;
	}
	
	/**
	 *
	 * @access protected
	 */
	protected function getCrumbs()
	{
		return $this->container->get('ccdn_component_crumb.trail');
	}
}