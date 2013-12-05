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

use CCDNMessage\MessageBundle\Controller\UserMessageBaseController;
use CCDNMessage\MessageBundle\Component\Dispatcher\MessageEvents;
use CCDNMessage\MessageBundle\Component\Dispatcher\Event\UserMessageResponseEvent;
use CCDNMessage\MessageBundle\Entity\Folder;

/**
 *
 * @category CCDNMessage
 * @package  MessageBundle
 *
 * @author   Reece Fowell <reece@codeconsortium.com>
 * @license  http://opensource.org/licenses/MIT MIT
 * @version  Release: 2.0
 * @link     https://github.com/codeconsortium/CCDNMessageMessageBundle
 *
 */
class UserMessageController extends UserMessageBaseController
{
    /**
     *
     * @access public
     * @param  int            $envelopeId
     * @return RenderResponse
     */
    public function showMessageAction($envelopeId)
    {
        $this->isAuthorised('ROLE_USER');
        $user = $this->getUser();
        $this->isFound($envelope = $this->getEnvelopeModel()->findEnvelopeByIdForUser($envelopeId, $user->getId()));
        $thread = $this->getThreadModel()->findThreadWithAllEnvelopesByThreadIdAndUserId($envelope->getThread()->getId(), $user->getId());
        $folders = $this->getFolderHelper()->findAllFoldersForUserById($user);
        $this->getEnvelopeModel()->markAsRead($envelope, $folders)->flush();

        return $this->renderResponse('CCDNMessageMessageBundle:User:Message/show.html.', array(
            'crumbs' => $this->getCrumbs()->addUserMessageShow($envelope),
            'folders' => $folders,
            'envelope' => $envelope,
            'thread' => $thread,
        ));
    }

    /**
     *
     * @access public
     * @param  int                             $userId
     * @return RedirectResponse|RenderResponse
     */
    public function composeAction($userId)
    {
        $this->isAuthorised('ROLE_USER');
        $formHandler = $this->getFormHandlerToSendMessage($userId);

        if ($formHandler->process()) {
            $response = $this->redirectResponse($this->path('ccdn_message_message_user_folder_show', array('folderName' => 'sent')));
        } else {
	        $folders = $this->getFolderHelper()->findAllFoldersForUserById($this->getUser());
			$currentFolder = $this->getFolderHelper()->filterFolderBySpecialType($folders, Folder::SPECIAL_TYPE_DRAFTS);
	        $response = $this->renderResponse('CCDNMessageMessageBundle:User:Message/compose.html.', array(
	            'crumbs' => $this->getCrumbs()->addUserMessageCreate($currentFolder),
	            'form' => $formHandler->getForm()->createView(),
	            'preview' => $formHandler->getForm()->getData(),
	        ));
        }
		
        $this->dispatch(MessageEvents::USER_MESSAGE_CREATE_RESPONSE, new UserMessageResponseEvent($this->getRequest(), $response, $formHandler->getForm()->getData()));
		
		return $response;
    }

    /**
     *
     * @access public
     * @param  int                             $envelopeId
     * @return RedirectResponse|RenderResponse
     */
    public function replyAction($envelopeId)
    {
        $this->isAuthorised('ROLE_USER');
        $this->isFound($envelope = $this->getEnvelopeModel()->findEnvelopeByIdForUser($envelopeId, $this->getUser()->getId()), 'Message could not be found.');
        $formHandler = $this->getFormHandlerToReplyToMessage($envelope);

        if ($formHandler->process()) {
            $response = $this->redirectResponse($this->path('ccdn_message_message_user_folder_show', array('folderName' => 'sent')));
        } else {
	        $response = $this->renderResponse('CCDNMessageMessageBundle:User:Message/compose_reply.html.', array(
	            'crumbs' => $this->getCrumbs()->addUserMessageReply($envelope),
	            'form' => $formHandler->getForm()->createView(),
	            'preview' => $formHandler->getForm()->getData(),
	            'envelope' => $envelope,
			));
        }
		
        $this->dispatch(MessageEvents::USER_MESSAGE_CREATE_REPLY_RESPONSE, new UserMessageResponseEvent($this->getRequest(), $response, $formHandler->getForm()->getData()));
		
		return $response;
    }

    /**
     *
     * @access public
     * @param  int                             $envelopeId
     * @return RedirectResponse|RenderResponse
     */
    public function forwardAction($envelopeId)
    {
        $this->isAuthorised('ROLE_USER');
        $this->isFound($envelope = $this->getEnvelopeModel()->findEnvelopeByIdForUser($envelopeId, $this->getUser()->getId()), 'Message could not be found.');
        $formHandler = $this->getFormHandlerToForwardMessage($envelope);

        if ($formHandler->process()) {
            $response = $this->redirectResponse($this->path('ccdn_message_message_user_folder_show', array('folderName' => 'sent')));
        } else {
	        $response = $this->renderResponse('CCDNMessageMessageBundle:User:Message/compose_forward.html.', array(
	            'crumbs' => $this->getCrumbs()->addUserMessageForward($envelope),
	            'form' => $formHandler->getForm()->createView(),
	            'preview' => $formHandler->getForm()->getData(),
	            'envelope' => $envelope,
			));
        }

        $this->dispatch(MessageEvents::USER_MESSAGE_CREATE_FORWARD_RESPONSE, new UserMessageResponseEvent($this->getRequest(), $response, $formHandler->getForm()->getData()));

		return $response;
    }

    /**
     * 
     * @access public
     * @param int $messageId
     * @return RedirectResponse
     */
    public function sendDraftAction($messageId)
    {
        $this->isAuthorised('ROLE_USER');
        $this->isFound($message = $this->getMessageModel()->findMessageByIdForUser($messageId, $this->getUser()->getId()));

        if (! $this->getFloodControl()->isFlooded()) {
            $this->getFloodControl()->incrementCounter();
            $this->getMessageModel()->sendDraft(array($message))->flush();
        } else {
            $this->setFlash('warning', $this->trans('flash.error.message.flood_control'));
        }

        $response = $this->redirectResponse($this->path('ccdn_message_message_user_folder_show', array('folderName' => 'sent' )));

        $this->dispatch(MessageEvents::USER_MESSAGE_DRAFT_SEND_RESPONSE, new UserMessageResponseEvent($this->getRequest(), $response, $message));
		
		return $response;
    }

    /**
     *
     * @access public
     * @param  int              $envelopeId
     * @return RedirectResponse
     */
    public function markAsReadAction($envelopeId)
    {
        $this->isAuthorised('ROLE_USER');
        $user = $this->getUser();
        $this->isFound($envelope = $this->getEnvelopeModel()->findEnvelopeByIdForUser($envelopeId, $user->getId()));
        $folders = $this->getFolderHelper()->findAllFoldersForUserById($user);
        $currentFolder = $this->getFolderHelper()->filterFolderByName($folders, $envelope->getFolder()->getName());
        $this->getEnvelopeModel()->markAsRead($envelope, $folders)->flush();

        $response = $this->redirectResponse($this->path('ccdn_message_message_user_folder_show', array('folderName' => $currentFolder->getName() )));
		
        $this->dispatch(MessageEvents::USER_MESSAGE_MARK_AS_READ_RESPONSE, new UserMessageResponseEvent($this->getRequest(), $response, $envelope->getMessage()));
		
		return $response;
    }

    /**
     *
     * @access public
     * @param  int              $envelopeId
     * @return RedirectResponse
     */
    public function markAsUnreadAction($envelopeId)
    {
        $this->isAuthorised('ROLE_USER');
        $user = $this->getUser();
        $this->isFound($envelope = $this->getEnvelopeModel()->findEnvelopeByIdForUser($envelopeId, $user->getId()));
        $folders = $this->getFolderHelper()->findAllFoldersForUserById($user);
        $currentFolder = $this->getFolderHelper()->filterFolderByName($folders, $envelope->getFolder()->getName());
        $this->getEnvelopeModel()->markAsUnread($envelope, $folders)->flush();

        $response = $this->redirectResponse($this->path('ccdn_message_message_user_folder_show', array('folderName' => $currentFolder->getName() )));
		
        $this->dispatch(MessageEvents::USER_MESSAGE_MARK_AS_UNREAD_RESPONSE, new UserMessageResponseEvent($this->getRequest(), $response, $envelope->getMessage()));

		return $response;
    }

    /**
     *
     * @access public
     * @param  int              $envelopeId
     * @return RedirectResponse
     */
    public function deleteAction($envelopeId)
    {
        $this->isAuthorised('ROLE_USER');
        $user = $this->getUser();
        $this->isFound($envelope = $this->getEnvelopeModel()->findEnvelopeByIdForUser($envelopeId, $user->getId()));
        $folders = $this->getFolderHelper()->findAllFoldersForUserById($user);
        $currentFolder = $this->getFolderHelper()->filterFolderByName($folders, $envelope->getFolder()->getName());
        $this->getEnvelopeModel()->delete($envelope, $folders)->flush();

        $response = $this->redirectResponse($this->path('ccdn_message_message_user_folder_show', array('folderName' => $currentFolder->getName() )));

        $this->dispatch(MessageEvents::USER_MESSAGE_DELETE_RESPONSE, new UserMessageResponseEvent($this->getRequest(), $response, $envelope->getMessage()));

		return $response;
    }
}
