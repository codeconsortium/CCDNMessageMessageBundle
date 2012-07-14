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

namespace CCDNMessage\MessageBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * 
 * @author Reece Fowell <reece@codeconsortium.com> 
 * @version 1.0
 */
class MessageController extends ContainerAware
{
 


	/**
	 *
	 * route: /en/messages/1/show
	 *
	 * @access public
	 * @param int $message_id
	 * @return RedirectResponse|RenderResponse
	 */
	public function showMessageAction($message_id)
	{
		if ( ! $this->container->get('security.context')->isGranted('ROLE_USER'))
		{
			throw new AccessDeniedException('You do not have access to this section.');
		}
			
		$user = $this->container->get('security.context')->getToken()->getUser();

		//
		// Get all the folders.
		//
		$folders = $this->container->get('ccdn_message_message.folder.repository')->findAllFoldersForUser($user->getId());
		
		$folderManager = $this->container->get('ccdn_message_message.folder.manager');
		
		if ( ! $folders)
		{
			$folderManager->setupDefaults($user->getId())->flushNow();

			$folders = $this->container->get('ccdn_message_message.folder.repository')->findAllFoldersForUser($user->getId());		        
		}

		//
		// Get the message.
		//
		$message = $this->container->get('ccdn_message_message.message.repository')->findMessageByIdForUser($message_id, $user->getId());
				
		if ( ! $message)
		{
			throw new NotFoundHttpException('No such message found!');
		}
		
		$quota = $this->container->getParameter('ccdn_message_message.quotas.max_messages');

		$currentFolder = $folderManager->getCurrentFolder($folders, $message->getFolder()->getName());
		$stats = $folderManager->getUsedAllowance($folders, $quota);
		$totalMessageCount = $stats['total_message_count'];
		$usedAllowance = $stats['used_allowance'];
		
		$this->container->get('ccdn_message_message.message.manager')->markAsRead($message)->flushNow()->updateAllFolderCachesForUser($user);
			
		$crumb_trail = $this->container->get('ccdn_component_crumb.trail')
			->add($this->container->get('translator')->trans('crumbs.message_index', array(), 'CCDNMessageMessageBundle'), $this->container->get('router')->generate('cc_message_index'), "home")
			->add($message->getFolder()->getName(), $this->container->get('router')->generate('cc_message_folder_show', array('folder_name' => $message->getFolder()->getName())), "folder")
			->add($message->getSubject(), $this->container->get('router')->generate('cc_message_message_show_by_id', array('message_id' => $message_id)), "email");
		
		return $this->container->get('templating')->renderResponse('CCDNMessageMessageBundle:Message:show.html.' . $this->getEngine(), array(
			'user_profile_route' => $this->container->getParameter('ccdn_message_message.user.profile_route'),
			'user' => $user,
			'crumbs' => $crumb_trail,
			'folders' => $folders,
			'current_folder' => $currentFolder,
			'used_allowance' => $usedAllowance,
			'message' => $message,
		));
	}
	
	
	
	/**
	 *
	 * @access public
	 * @param int $user_id
	 * @return RedirectResponse|RenderResponse
	 */   
    public function composeAction($user_id)
    {
		//
		//	Invalidate this action / redirect if user should not have access to it
		//
		if ( ! $this->container->get('security.context')->isGranted('ROLE_USER')) {
			throw new AccessDeniedException('You do not have permission to use this resource!');
		}
		
		$user = $this->container->get('security.context')->getToken()->getUser();
		
		//
		// Are we sending this to someone who's 'send message' button we clicked? 
		//
		if ($user_id)
		{
			$send_to = $this->container->get('ccdn_user_user.user.repository')->findOneById($user_id);
			$formHandler = $this->container->get('ccdn_message_message.message.form.handler')->setDefaultValues(array('sender' => $user, 'send_to' => $send_to));
		} else {
			$formHandler = $this->container->get('ccdn_message_message.message.form.handler')->setDefaultValues(array('sender' => $user));
		}	
		
		if (isset($_POST['submit_draft']))
		{
			$formHandler->setMode($formHandler::DRAFT);
			
			if ($formHandler->process())	
			{
				return new RedirectResponse($this->container->get('router')->generate('cc_message_folder_show', array('folder_name' => 'drafts')));
			}
		}

		if (isset($_POST['submit_post']))
		{
			if ($formHandler->process())	
			{
				return new RedirectResponse($this->container->get('router')->generate('cc_message_folder_show', array('folder_name' => 'sent')));
			}
		}
		
		if (isset($_POST['submit_preview']))
		{
			$formHandler->setMode($formHandler::PREVIEW);
		}
		
		//
		// Get all the folders.
		//
		$folders = $this->container->get('ccdn_message_message.folder.repository')->findAllFoldersForUser($user->getId());
		
		$folderManager = $this->container->get('ccdn_message_message.folder.manager');
		
		if ( ! $folders)
		{
			$folderManager->setupDefaults($user->getId())->flushNow();

			$folders = $this->container->get('ccdn_message_message.folder.repository')->findAllFoldersForUser($user->getId());		        
		}
		
		// setup crumb trail.
		$crumb_trail = $this->container->get('ccdn_component_crumb.trail')
			->add($this->container->get('translator')->trans('crumbs.message_index', array(), 'CCDNMessageMessageBundle'), $this->container->get('router')->generate('cc_message_index'), "home")
			->add($this->container->get('translator')->trans('crumbs.compose_message', array(), 'CCDNMessageMessageBundle'), $this->container->get('router')->generate('cc_message_message_compose'), "edit");
				
		return $this->container->get('templating')->renderResponse('CCDNMessageMessageBundle:Message:compose.html.' . $this->getEngine(), array(
			'user_profile_route' => $this->container->getParameter('ccdn_message_message.user.profile_route'),
			'crumbs' => $crumb_trail,
			'form' => $formHandler->getForm()->createView(),
			'preview' => $formHandler->getForm()->getData(),
			'folders' => $folders,
			'user' => $user,
		));
    }



	/**
	 *
	 * @access public
	 * @param int $message_id
	 * @return RedirectResponse|RenderResponse
	 */
	public function replyAction($message_id)
	{
		//
		//	Invalidate this action / redirect if user should not have access to it
		//
		if ( ! $this->container->get('security.context')->isGranted('ROLE_USER')) {
			throw new AccessDeniedException('You do not have permission to use this resource!');
		}

		$user = $this->container->get('security.context')->getToken()->getUser();

		$message = $this->container->get('ccdn_message_message.message.repository')->findMessageByIdForUser($message_id, $user->getId());

		if ( ! $message)
		{
			throw new NotFoundHttpException('No such message found!');
		}
		
		$formHandler = $this->container->get('ccdn_message_message.message.form.handler')->setDefaultValues(array('sender' => $user, 'message' => $message, 'action' => 'reply'));

		if ($formHandler->process())	
		{				
			$this->container->get('session')->setFlash('notice', $this->container->get('translator')->trans('flash.message.sent.success', array(), 'CCDNMessageMessageBundle'));

			return new RedirectResponse($this->container->get('router')->generate('cc_message_folder_show', array('folder_name' => 'sent')));
		}
		else
		{	
			//
			// Get all the folders.
			//
			$folders = $this->container->get('ccdn_message_message.folder.repository')->findAllFoldersForUser($user->getId());

			$folderManager = $this->container->get('ccdn_message_message.folder.manager');

			if ( ! $folders)
			{
				$folderManager->setupDefaults($user->getId())->flushNow();

				$folders = $this->container->get('ccdn_message_message.folder.repository')->findAllFoldersForUser($user->getId());		        
			}
			
			// setup crumb trail.
			$crumb_trail = $this->container->get('ccdn_component_crumb.trail')
				->add($this->container->get('translator')->trans('crumbs.message_index', array(), 'CCDNMessageMessageBundle'), $this->container->get('router')->generate('cc_message_index'), "home")
				->add($message->getSubject(), $this->container->get('router')->generate('cc_message_message_show_by_id', array('message_id' => $message_id)), "email")
				->add($this->container->get('translator')->trans('crumbs.compose_reply', array(), 'CCDNMessageMessageBundle'), $this->container->get('router')->generate('cc_message_message_compose_reply', array('message_id' => $message_id)), "edit");

			return $this->container->get('templating')->renderResponse('CCDNMessageMessageBundle:Message:compose.html.' . $this->getEngine(), array(
				'user_profile_route' => $this->container->getParameter('ccdn_message_message.user.profile_route'),
				'crumbs' => $crumb_trail,
				'form' => $formHandler->getForm()->createView(),
				'preview' => $formHandler->getForm()->getData(),
				'folders' => $folders,
				'user' => $user,
			));
		}
	}
	
	
	
	/**
	 *
	 * @access public
	 * @param int $message_id
	 * @return RedirectResponse|RenderResponse
	 */
	public function forwardAction($message_id)
	{
		//
		//	Invalidate this action / redirect if user should not have access to it
		//
		if ( ! $this->container->get('security.context')->isGranted('ROLE_USER')) {
			throw new AccessDeniedException('You do not have permission to use this resource!');
		}

		$user = $this->container->get('security.context')->getToken()->getUser();

		$message = $this->container->get('ccdn_message_message.message.repository')->findMessageByIdForUser($message_id, $user->getId());

		if ( ! $message)
		{
			throw new NotFoundHttpException('No such message found!');
		}
		
		$formHandler = $this->container->get('ccdn_message_message.message.form.handler')->setDefaultValues(array('sender' => $user, 'message' => $message, 'action' => 'forward'));

		if ($formHandler->process())	
		{
			$this->container->get('session')->setFlash('notice', $this->container->get('translator')->trans('flash.message.sent.success', array(), 'CCDNMessageMessageBundle'));

			return new RedirectResponse($this->container->get('router')->generate('cc_message_folder_show', array('folder_name' => 'sent')));
		}
		else
		{
			//
			// Get all the folders.
			//
			$folders = $this->container->get('ccdn_message_message.folder.repository')->findAllFoldersForUser($user->getId());

			$folderManager = $this->container->get('ccdn_message_message.folder.manager');

			if ( ! $folders)
			{
				$folderManager->setupDefaults($user->getId())->flushNow();

				$folders = $this->container->get('ccdn_message_message.folder.repository')->findAllFoldersForUser($user->getId());		        
			}
			
			// setup crumb trail.
			$crumb_trail = $this->container->get('ccdn_component_crumb.trail')
				->add($this->container->get('translator')->trans('crumbs.message_index', array(), 'CCDNMessageMessageBundle'), $this->container->get('router')->generate('cc_message_index'), "home")
				->add($message->getSubject(), $this->container->get('router')->generate('cc_message_message_show_by_id', array('message_id' => $message_id)), "email")
				->add($this->container->get('translator')->trans('crumbs.compose_forward', array(), 'CCDNMessageMessageBundle'), $this->container->get('router')->generate('cc_message_message_compose_forward', array('message_id' => $message_id)), "edit");

			return $this->container->get('templating')->renderResponse('CCDNMessageMessageBundle:Message:compose.html.' . $this->getEngine(), array(
				'user_profile_route' => $this->container->getParameter('ccdn_message_message.user.profile_route'),
				'crumbs' => $crumb_trail,
				'form' => $formHandler->getForm()->createView(),
				'preview' => $formHandler->getForm()->getData(),
				'folders' => $folders,
				'user' => $user,
			));
		}		
	}
	
	
	
	/**
	* @access public
	* @param int $message_id
	* @return RedirectResponse|RenderResponse
	*/
	public function sendDraftAction($message_id)	
	{
		//
		//	Invalidate this action / redirect if user should not have access to it
		//
		if ( ! $this->container->get('security.context')->isGranted('ROLE_USER')) {
			throw new AccessDeniedException('You do not have permission to use this resource!');
		}

		$user = $this->container->get('security.context')->getToken()->getUser();

		$message = $this->container->get('ccdn_message_message.message.repository')->findMessageByIdForUser($message_id, $user->getId());

		if ( ! $message)
		{
			throw new NotFoundHttpException('No such message found!');
		}
		
		$this->container->get('ccdn_message_message.message.manager')->sendDraft(array($message))->flushNow();
		
		return new RedirectResponse($this->container->get('router')->generate('cc_message_folder_show', array('folder_name' => 'sent')));
	}
	
	
	
	/**
	 *
	 * @access public
	 * @param int $message_id
	 * @return RedirectResponse
	 */
	public function markAsReadAction($message_id)
	{
		if ( ! $this->container->get('security.context')->isGranted('ROLE_USER'))
		{
			throw new AccessDeniedException('You do not have access to this section.');
		}
		
		$user = $this->container->get('security.context')->getToken()->getUser();

		$message = $this->container->get('ccdn_message_message.message.repository')->findMessageByIdForUser($message_id, $user->getId());

		if ( ! $message)
		{
			throw new NotFoundHttpException('No such message found!');
		}		
		
		$this->container->get('ccdn_message_message.message.manager')->markAsRead($message)->flushNow()->updateAllFolderCachesForUser($user);
				
		return new RedirectResponse($this->container->get('router')->generate('cc_message_folder_show', array('folder_name' => $message->getFolder()->getName())));
	}
	
	
	
	/**
	 *
	 * @access public
	 * @param int $message_id
	 * @return RedirectResponse|RenderResponse
	 */
	public function markAsUnreadAction($message_id)
	{
		if ( ! $this->container->get('security.context')->isGranted('ROLE_USER'))
		{
			throw new AccessDeniedException('You do not have access to this section.');
		}
		
		$user = $this->container->get('security.context')->getToken()->getUser();

		$message = $this->container->get('ccdn_message_message.message.repository')->findMessageByIdForUser($message_id, $user->getId());

		if ( ! $message)
		{
			throw new NotFoundHttpException('No such message found!');
		}
		
		$this->container->get('ccdn_message_message.message.manager')->markAsUnread($message)->flushNow()->updateAllFolderCachesForUser($user);
	
		return new RedirectResponse($this->container->get('router')->generate('cc_message_folder_show', array('folder_name' => $message->getFolder()->getName())));
	}



	/**
	 *
	 * @access public
	 * @param int $message_id
	 * @return RedirectResponse
	 */
	public function deleteAction($message_id)
	{
		if ( ! $this->container->get('security.context')->isGranted('ROLE_USER'))
		{
			throw new AccessDeniedException('You do not have access to this section.');
		}
		
		$user = $this->container->get('security.context')->getToken()->getUser();

		$message = $this->container->get('ccdn_message_message.message.repository')->findMessageByIdForUser($message_id, $user->getId());
		$folders = $this->container->get('ccdn_message_message.folder.repository')->findAllFoldersForUser($user->getId());		        
		
		if ( ! $folders)
		{
			$this->container->get('ccdn_message_message.folder.manager')->setupDefaults($user->getId())->flushNow();

			$folders = $this->container->get('ccdn_message_message.folder.repository')->findAllFoldersForUser($user->getId());		        
		}
		
		if ( ! $message)
		{
			throw new NotFoundHttpException('No such message found!');
		}
		
		$this->container->get('ccdn_message_message.message.manager')->delete($message, $folders)->flushNow()->updateAllFolderCachesForUser($user);
		
		return new RedirectResponse($this->container->get('router')->generate('cc_message_folder_show', array('folder_name' => $message->getFolder()->getName())));
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

}
