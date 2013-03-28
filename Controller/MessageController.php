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

use CCDNMessage\MessageBundle\Controller\MessageBaseController;

/**
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
class MessageController extends MessageBaseController
{
    /**
     *
     * @access public
     * @param  int $messageId
     * @return RenderResponse
     */
    public function showMessageAction($messageId)
    {
        $this->isAuthorised('ROLE_USER');

		$user = $this->getUser();
		
        // Get the message.
        $message = $this->getMessageManager()->findMessageByIdForUser($messageId, $user->getId());
        $this->isFound($message);

        $folders = $this->getFolderManager()->findAllFoldersForUserById($user->getId());
        $currentFolder = $this->getFolderManager()->getCurrentFolder($folders, $message->getFolder()->getName());

        $this->getMessageManager()->markAsRead($message)->flush()->updateAllFolderCachesForUser($user);

        $crumbs = $this->getCrumbs()
            ->add($this->trans('ccdn_message_message.crumbs.message_index'), $this->path('ccdn_message_message_index'))
            ->add($message->getFolder()->getName(), $this->path('ccdn_message_message_folder_show', array('folderName' => $message->getFolder()->getName())))
            ->add($message->getSubject(), $this->path('ccdn_message_message_mail_show_by_id', array('messageId' => $messageId)));

        return $this->renderResponse('CCDNMessageMessageBundle:Message:show.html.',
			array(
	            'crumbs' => $crumbs,
	            'folders' => $folders,
	            'message' => $message,
	        )
		);
    }

    /**
     *
     * @access public
     * @param  int $userId
     * @return RedirectResponse|RenderResponse
     */
    public function composeAction($userId)
    {
        $this->isAuthorised('ROLE_USER');

		$formHandler = $this->getFormHandlerToSendMessage($userId);
		
		// Flood Control.
		if (! $this->getFloodControl()->isFlooded()) {
            if ($formHandler->process($this->getRequest())) {
				$this->getFloodControl()->incrementCounter();
				
                return $this->redirectResponse($this->path('ccdn_message_message_folder_show', array('folderName' => 'sent')));
            }
		} else {
			$this->setFlash('warning', $this->trans('ccdn_message_message.flash.send.flood_control'));
		}

        // setup crumb trail.
        $crumbs = $this->getCrumbs()
            ->add($this->trans('ccdn_message_message.crumbs.message_index'), $this->path('ccdn_message_message_index'))
            ->add($this->trans('ccdn_message_message.crumbs.compose_message'), $this->path('ccdn_message_message_mail_compose'));

        return $this->renderResponse('CCDNMessageMessageBundle:Message:compose.html.',
			array(
	            'crumbs' => $crumbs,
	            'form' => $formHandler->getForm()->createView(),
	            'preview' => $formHandler->getForm()->getData(),
	        )
		);
    }

    /**
     *
     * @access public
     * @param  int $messageId
     * @return RedirectResponse|RenderResponse
     */
    public function replyAction($messageId)
    {
        $this->isAuthorised('ROLE_USER');

		$formHandler = $this->getFormHandlerToSendMessage(null, $messageId);
		
		// Flood Control.
		if (! $this->getFloodControl()->isFlooded()) {
	        if ($formHandler->process($this->getRequest())) {
	     		$this->getFloodControl()->incrementCounter();
	       		
				$this->setFlash('notice', $this->trans('ccdn_message_message.flash.message.sent.success'));

	            return $this->redirectResponse($this->path('ccdn_message_message_folder_show', array('folder_name' => 'sent')));
	        }
		} else {
			$this->setFlash('warning', $this->trans('ccdn_message_message.flash.send.flood_control'));
		}

        // setup crumb trail.
        $crumbs = $this->getCrumbs()
            ->add($this->trans('ccdn_message_message.crumbs.message_index'), $this->path('ccdn_message_message_index'))
            ->add($message->getSubject(), $this->path('ccdn_message_message_mail_show_by_id', array('messageId' => $messageId)))
            ->add($this->trans('ccdn_message_message.crumbs.compose_reply'), $this->path('ccdn_message_message_mail_compose_reply', array('messageId' => $messageId)));

        return $this->renderResponse('CCDNMessageMessageBundle:Message:compose.html.',
			array(
	            'crumbs' => $crumbs,
	            'form' => $formHandler->getForm()->createView(),
	            'preview' => $formHandler->getForm()->getData(),
	        )
		);
    }

    /**
     *
     * @access public
     * @param  int $messageId
     * @return RedirectResponse|RenderResponse
     */
    public function forwardAction($messageId)
    {
        $this->isAuthorised('ROLE_USER');

		$formHandler = $this->getFormHandlerToSendMessage(null, null, $messageId);

		// Flood Control.
		if (! $this->getFloodControl()->isFlooded()) {
	        if ($formHandler->process($this->getRequest())) {
				$this->getFloodControl()->incrementCounter();
				
	            $this->setFlash('notice', $this->trans('ccdn_message_message.flash.message.sent.success'));

	            return $this->redirectResponse($this->path('ccdn_message_message_folder_show', array('folder_name' => 'sent')));
	        }
		} else {
			$this->setFlash('warning', $this->trans('ccdn_message_message.flash.send.flood_control'));
		}

		// setup crumb trail.
		$crumbs = $this->getCrumbs()
		    ->add($this->trans('ccdn_message_message.crumbs.message_index'), $this->path('ccdn_message_message_index'))
		    ->add($message->getSubject(), $this->path('ccdn_message_message_mail_show_by_id', array('messageId' => $messageId)))
		    ->add($this->trans('ccdn_message_message.crumbs.compose_forward'), $this->path('ccdn_message_message_mail_compose_forward', array('messageId' => $messageId)));

		return $this->renderResponse('CCDNMessageMessageBundle:Message:compose.html.',
			array(
			    'crumbs' => $crumbs,
			    'form' => $formHandler->getForm()->createView(),
			    'preview' => $formHandler->getForm()->getData(),
			)
		);
    }

    /**
    * @access public
    * @param int $messageId
    * @return RedirectResponse
    */
    public function sendDraftAction($messageId)
    {
        $this->isAuthorised('ROLE_USER');

		$user = $this->getUser();

        $message = $this->getMessageManager()->findMessageByIdForUser($messageId, $user->getId());
        $this->isFound($message);

		// Flood Control.
		if (! $this->getFloodControl()->isFlooded()) {
			$this->getFloodControl()->incrementCounter();
			
	        $this->getMessageManager()->sendDraft(array($message))->flush();
		} else {
			$this->setFlash('warning', $this->trans('ccdn_message_message.flash.send.flood_control'));
		}
		
        return $this->redirectResponse($this->path('ccdn_message_message_folder_show',
			array(
				'folder_name' => 'sent'
			)
		));
    }

    /**
     *
     * @access public
     * @param  int $messageId
     * @return RedirectResponse
     */
    public function markAsReadAction($messageId)
    {
        $this->isAuthorised('ROLE_USER');

		$user = $this->getUser();

        $message = $this->getMessageManager()->findMessageByIdForUser($messageId, $user->getId());
        $this->isFound($message);

        $this->getMessageManager()->markAsRead($message)->flush()->updateAllFolderCachesForUser($user);

        return $this->redirectResponse($this->path('ccdn_message_message_folder_show',
			array(
				'folder_name' => $message->getFolder()->getName()
			)
		));
    }

    /**
     *
     * @access public
     * @param  int $messageId
     * @return RedirectResponse
     */
    public function markAsUnreadAction($messageId)
    {
        $this->isAuthorised('ROLE_USER');

		$user = $this->getUser();
		
        $message = $this->getMessageManager()->findMessageByIdForUser($messageId, $user->getId());
        $this->isFound($message);

        $this->getMessageManager()->markAsUnread($message)->flush()->updateAllFolderCachesForUser($user);

        return $this->redirectResponse($this->path('ccdn_message_message_folder_show',
			array(
				'folder_name' => $message->getFolder()->getName()
			)
		));
    }

    /**
     *
     * @access public
     * @param  int $messageId
     * @return RedirectResponse
     */
    public function deleteAction($messageId)
    {
        $this->isAuthorised('ROLE_USER');

        $user = $this->getUser();

        $message = $this->getMessageManager()->findMessageByIdForUser($messageId, $user->getId());
        $this->isFound($message);

        $folders = $this->getFolderManager()->findAllFoldersForUserById($user->getId());

        $this->getMessageManager()->delete($message, $folders)->flush()->updateAllFolderCachesForUser($user);

        return $this->redirectResponse($this->path('ccdn_message_message_folder_show',
			array(
				'folder_name' => $message->getFolder()->getName()
			)
		));
    }
}