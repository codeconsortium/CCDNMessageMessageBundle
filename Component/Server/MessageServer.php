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

namespace CCDNMessage\MessageBundle\Component\Server;

use Symfony\Component\HttpKernel\Debug\ContainerAwareTraceableEventDispatcher;
use Symfony\Component\Security\Core\User\UserInterface;

use CCDNMessage\MessageBundle\Component\Dispatcher\MessageEvents;
use CCDNMessage\MessageBundle\Component\Dispatcher\Event\UserMessageFloodEvent;
use CCDNMessage\MessageBundle\Component\Dispatcher\Event\UserMessageEvent;

use CCDNMessage\MessageBundle\Model\Model\FolderModel;
use CCDNMessage\MessageBundle\Model\Model\MessageModel;
use CCDNMessage\MessageBundle\Model\Model\EnvelopeModel;
use CCDNMessage\MessageBundle\Model\Model\UserModel;

use CCDNMessage\MessageBundle\Entity\Folder;
use CCDNMessage\MessageBundle\Entity\Message;
use CCDNMessage\MessageBundle\Entity\Envelope;
use CCDNMessage\MessageBundle\Entity\Thread;

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
class MessageServer
{
	/**
	 * 
	 * @access protected
     * @var \Symfony\Component\HttpKernel\Debug\ContainerAwareTraceableEventDispatcher $dispatcher
	 */
	protected $dispatcher;

	/**
	 * 
	 * @access protected
     * @var \CCDNMessage\MessageBundle\Model\Model\FolderModel $folderModel
	 */
	protected $folderModel;

	/**
	 * 
	 * @access protected
     * @var \CCDNMessage\MessageBundle\Model\Model\MessageModel $messageModel
	 */
	protected $messageModel;

	/**
	 * 
	 * @access protected
     * @var \CCDNMessage\MessageBundle\Model\Model\EnvelopeModel $envelopeModel
	 */
	protected $envelopeModel;

	/**
	 * 
	 * @access protected
     * @var \CCDNMessage\MessageBundle\Model\Model\UserModel $userModel
	 */
	protected $userModel;
	
    const MESSAGE_SEND = 0;
    const MESSAGE_SAVE_CARBON_COPY = 1;
    const MESSAGE_SAVE_DRAFT = 2;

    private $sendMode = array(
        self::MESSAGE_SEND,
        self::MESSAGE_SAVE_CARBON_COPY,
        self::MESSAGE_SAVE_DRAFT,
    );

	/**
	 * 
	 * @access public
	 * @param  \Symfony\Component\HttpKernel\Debug\ContainerAwareTraceableEventDispatcher $dispatcher
	 * @param  \CCDNMessage\MessageBundle\Model\Model\FolderModel                         $folderModel
	 * @param  \CCDNMessage\MessageBundle\Model\Model\MessageModel                        $messageModel
	 * @param  \CCDNMessage\MessageBundle\Model\Model\EnvelopeModel                       $envelopeModel
	 * @param  \CCDNMessage\MessageBundle\Model\Model\UserModel                           $userModel
	 */
	public function __construct(ContainerAwareTraceableEventDispatcher $dispatcher, FolderModel $folderModel, MessageModel $messageModel, EnvelopeModel $envelopeModel, UserModel $userModel)
	{
		$this->dispatcher    = $dispatcher;
		$this->envelopeModel = $envelopeModel;
		$this->messageModel  = $messageModel;
		$this->folderModel   = $folderModel;
		$this->userModel     = $userModel;
	}

	/**
	 * 
	 * @access public
	 * @param  \CCDNMessage\MessageBundle\Entity\Message $message
	 * @param  bool                                      $isFlagged
	 */
	public function sendMessage(Message $message, $isFlagged)
	{
        // Create a new Thread.
		if (null == $message->getThread()) {
	        $thread = new Thread();
	        $message->setThread($thread);
		}

        $this->messageModel->saveMessage($message)->refresh($message);

        $recipients = $this->getRecipients($message);
        foreach ($recipients as $recipientKey => $recipient) {
            $this->receiveMessage($message, $recipient, self::MESSAGE_SEND, $isFlagged);
        }
        
        // Add Carbon Copy.
        $this->receiveMessage($message, $message->getSentFromUser(), self::MESSAGE_SAVE_CARBON_COPY, $isFlagged);
		
        //if ($mode != self::MESSAGE_SAVE_CARBON_COPY && (! is_object($ownedByUser) || ! $ownedByUser instanceof UserInterface)) {
        //    throw new \Exception("Message Owner parameter must be set.");
        //}
        //
        //if (! in_array($mode, $this->sendMode)) {
        //    throw new \Exception('Invalid mode, use class constants in $sendMode');
        //}
        //
        //$folderManager = $this->managerBag->getFolderManager();
        //$folders = $folderManager->findAllFoldersForUserById($ownedByUser->getId());
        //
        //if (null == $folders) {
        //    return false;
        //}
        //
        //// Check quotas.
        //$quotaUsed = $folderManager->checkQuotaAllowanceUsed($folders);
        //$quotaAllowed = $this->getQuotaMaxAllowanceForMessages();
        //
        //if ($quotaUsed >= $quotaAllowed) {
        //    //$this->container->get('session')->setFlash('notice',
        //    //    $this->container->get('translator')->trans('ccdn_message_message.flash.message.send.inbox_full', array('%user%' => $recipient->getUsername()), 'CCDNMessageMessageBundle'));
        //    return false;
        //}
        //
        //// Update recipients folders read/unread cache counts.
        //$this->managerBag->getFolderManager()->updateAllFolderCachesForUser($ownedByUser, $folders)->flush();
	}

	/**
	 * 
	 * @access public
	 * @param  \CCDNMessage\MessageBundle\Entity\Message $message
	 * @param  bool                                      $isFlagged
	 */
	public function saveDraft(Message $message, $isFlagged)
	{
	    $this->receiveMessage($message, $message->getSentFromUser(), self::MESSAGE_SAVE_DRAFT, $isFlagged);
	}

	/**
	 * 
	 * @access public
	 * @param  \CCDNMessage\MessageBundle\Entity\Message           $message
	 * @param  \Symfony\Component\Security\Core\User\UserInterface $recipient
	 * @param  int                                                 $mode
	 * @param  bool                                                $isFlagged
	 */
	public function receiveMessage(Message $message, UserInterface $recipient, $mode, $isFlagged = false)
	{
		if (! in_array($mode, $this->sendMode)) {
			throw new \Exception('Message send mode not valid!');
		}
		
		$envelope = new Envelope();
		$envelope->setMessage($message);
		$envelope->setThread($message->getThread());
		$envelope->setOwnedByUser($recipient);
		$envelope->setIsFlagged($isFlagged);
		$envelope->setSentDate(new \Datetime('now'));
		
		// 1 find inbox for recipient
		switch ($mode) {
			case self::MESSAGE_SEND: 
				$folder = $this->folderModel->findOneFolderForUserByNameAndUserId('inbox', $recipient->getId());
				$envelope->setSentToUser($recipient);
				break;
			case self::MESSAGE_SAVE_CARBON_COPY:
				$folder = $this->folderModel->findOneFolderForUserByNameAndUserId('sent', $recipient->getId());
				$envelope->setIsRead(true);
				break;
			case self::MESSAGE_SAVE_DRAFT:
				$folder = $this->folderModel->findOneFolderForUserByNameAndUserId('drafts', $recipient->getId());
				$envelope->setIsRead(true);
				$envelope->setIsDraft(true);
				break;
		}

		$envelope->setFolder($folder);
		$this->envelopeModel->saveEnvelope($envelope);
		
		return $envelope;
	}

    /**
     *
     * @access public
     * @param  \CCDNMessage\MessageBundle\Entity\Message               $message
     * @return \CCDNMessage\MessageBundle\Model\Manager\MessageManager
     */
    public function getRecipients(Message $message)
    {
        $recipients = $message->getSendTo();

        // build a list of recipients from the sendTo field.
        if ($recipients = preg_split('/((,)|(\s))/', $recipients, PREG_OFFSET_CAPTURE)) {
            foreach ($recipients as $key => $recipient) {
                $recipients[$key] = preg_replace("/[^a-zA-Z0-9_]/", "", $recipients[$key]);

                if (! $recipient) {
                    unset($recipients[$key]);
                }
            }

            $users = $this->userModel->findTheseUsersByUsername($recipients);
        } else {
			// If preg split fails, then presumably theres no comma delimiter, so only one user.
            $users = $this->userModel->findTheseUsersByUsername(array($recipients));
        }

        return $users;
    }
}
