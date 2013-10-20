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
        $thread = $this->getThreadModel()->findThreadWithAllEnvelopesByThreadIDAndUserId($envelope->getThread()->getId(), $user->getId());
        $folders = $this->getFolderModel()->findAllFoldersForUserById($user->getId());
        $currentFolder = $this->getFolderModel()->getCurrentFolder($folders, $envelope->getFolder()->getName());

        $this->getEnvelopeModel()->markAsRead($envelope, $folders)->flush();
        $message = $envelope->getMessage();

        $crumbs = $this->getCrumbs()
            ->add($envelope->getFolder()->getName(), $this->path('ccdn_message_message_user_folder_show', array('folderName' => $envelope->getFolder()->getName())))
            ->add($message->getSubject(), $this->path('ccdn_message_message_user_mail_show_by_id', array('envelopeId' => $envelopeId)));

        return $this->renderResponse('CCDNMessageMessageBundle:User:Message/show.html.', array(
            'crumbs' => $crumbs,
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
	        $crumbs = $this->getCrumbs()
	            ->add($this->trans('crumbs.folder.index'), $this->path('ccdn_message_message_user_index'))
	            ->add($this->trans('crumbs.message.compose.new'), $this->path('ccdn_message_message_user_mail_compose'));

	        $response = $this->renderResponse('CCDNMessageMessageBundle:User:Message/compose.html.', array(
	            'crumbs' => $crumbs,
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
	        $crumbs = $this->getCrumbs()
	            ->add($this->trans('crumbs.folder.index'), $this->path('ccdn_message_message_user_index'))
	            ->add($envelope->getMessage()->getSubject(), $this->path('ccdn_message_message_user_mail_show_by_id', array('envelopeId' => $envelopeId)))
	            ->add($this->trans('crumbs.message.compose.reply'), $this->path('ccdn_message_message_user_mail_compose_reply', array('envelopeId' => $envelopeId)));

	        $response = $this->renderResponse('CCDNMessageMessageBundle:User:Message/compose_reply.html.', array(
	            'crumbs' => $crumbs,
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
	        $crumbs = $this->getCrumbs()
	            ->add($this->trans('crumbs.folder.index'), $this->path('ccdn_message_message_user_index'))
	            ->add($envelope->getMessage()->getSubject(), $this->path('ccdn_message_message_user_mail_show_by_id', array('envelopeId' => $envelopeId)))
	            ->add($this->trans('crumbs.message.compose.forward'), $this->path('ccdn_message_message_user_mail_compose_forward', array('envelopeId' => $envelopeId)));

	        $response = $this->renderResponse('CCDNMessageMessageBundle:User:Message/compose_forward.html.', array(
	            'crumbs' => $crumbs,
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

        //$this->dispatch(MessageEvents::USER_MESSAGE_CREATE_RESPONSE, new UserMessageResponseEvent($this->getRequest(), $response, $formHandler->getForm()->getData()));

        return $this->redirectResponse($this->path('ccdn_message_message_user_folder_show', array('folderName' => 'sent' )));
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
        $folders = $this->getFolderModel()->findAllFoldersForUserById($user->getId());
        $currentFolder = $this->getFolderModel()->getCurrentFolder($folders, $envelope->getFolder()->getName());
        $this->getEnvelopeModel()->markAsRead($envelope, $folders)->flush();

        //$this->dispatch(MessageEvents::USER_MESSAGE_CREATE_RESPONSE, new UserMessageResponseEvent($this->getRequest(), $response, $formHandler->getForm()->getData()));

        return $this->redirectResponse($this->path('ccdn_message_message_user_folder_show', array('folderName' => $currentFolder->getName() )));
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
        $folders = $this->getFolderModel()->findAllFoldersForUserById($user->getId());
        $currentFolder = $this->getFolderModel()->getCurrentFolder($folders, $envelope->getFolder()->getName());
        $this->getEnvelopeModel()->markAsUnread($envelope, $folders)->flush();

        //$this->dispatch(MessageEvents::USER_MESSAGE_CREATE_RESPONSE, new UserMessageResponseEvent($this->getRequest(), $response, $formHandler->getForm()->getData()));

        return $this->redirectResponse($this->path('ccdn_message_message_user_folder_show', array('folderName' => $currentFolder->getName() )));
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
        $folders = $this->getFolderModel()->findAllFoldersForUserById($user->getId());
        $currentFolder = $this->getFolderModel()->getCurrentFolder($folders, $envelope->getFolder()->getName());
        $this->getEnvelopeModel()->delete($envelope, $folders)->flush();

        //$this->dispatch(MessageEvents::USER_MESSAGE_CREATE_RESPONSE, new UserMessageResponseEvent($this->getRequest(), $response, $formHandler->getForm()->getData()));

        return $this->redirectResponse($this->path('ccdn_message_message_user_folder_show', array('folderName' => $currentFolder->getName() )));
    }
}
