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

namespace CCDNMessage\MessageBundle\Manager;

use CCDNMessage\MessageBundle\Manager\ManagerInterface;
use CCDNMessage\MessageBundle\Manager\BaseManager;

use CCDNMessage\MessageBundle\Entity\Message;

/**
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
class MessageManager extends BaseManager implements ManagerInterface
{

	/**
	 *
	 * @access public
	 * @param Message $message
	 * @return self
	 */
    public function saveDraft($message)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        $folderRepo = $this->container->get('ccdn_message_message.repository.folder');
        $folderManager = $this->container->get('ccdn_message_message.manager.folder');
        $quota = $this->container->getParameter('ccdn_message_message.quotas.max_messages');

        //
        // Get the folders for this user
        //
        $folders = $folderRepo->findAllFoldersForUser($user->getId());

        //
        // Ensure folders exist or create them
        //
        if (! $folders) {
            $folderManager->setupDefaults($recipient->getId())->flush();

            $folders = $folderRepo->findAllFoldersForUser($recipient->getId());
        }

        //
        // Check the used space against the quota
        //
        $used = $folderManager->checkQuotaAllowanceUsed($folders);

        if ($used >= $quota) {
            $this->container->get('session')->setFlash('notice',
                $this->container->get('translator')->trans('ccdn_message_message.flash.message.send.inbox_full', array('%user%' => $user->getUsername()), 'CCDNMessageMessageBundle'));
        } else {
            $this->container->get('session')->setFlash('notice',
                $this->container->get('translator')->trans('ccdn_message_message.flash.message.draft.saved', array('%user%' => $user->getUsername()), 'CCDNMessageMessageBundle'));
        }

        $message->setOwnedBy($user);
        $message->setSentFrom($user);
        $message->setIsDraft(true);
        $message->setIsRead(false);
        $message->setFolder($folders[2]);

        $this->persist($message);
        $this->flush();

        $this->updateAllFolderCachesForUser($user);

        return $this;
    }

    /**
     *
     * @access public
     * @param array $message
     * @return self
     */
    public function sendDraft(array $messages)
    {
        foreach ($messages as $message) {
            $this->insert($message);
        }

        return $this;
    }

    /**
     *
     * @access public
     * @param string $message, string $recipient, string $sender, bool $isCarbonCopy
     * @return self
     */
    public function sendTo($message, $recipient, $sender, $isCarbonCopy)
    {
        $folderRepo = $this->container->get('ccdn_message_message.repository.folder');
        $folderManager = $this->container->get('ccdn_message_message.manager.folder');
        $quota = $this->container->getParameter('ccdn_message_message.quotas.max_messages');

        //
        // If recipient is null, then this is likely the carbon copy.
        //
        if ($recipient) {
            $folders = $folderRepo->findAllFoldersForUser($recipient->getId());
        } else {
            if ($sender) {
                $folders = $folderRepo->findAllFoldersForUser($sender->getId());
            } else {
                return $this;
            }
        }

        if (! $folders) {
            $folderManager->setupDefaults($recipient->getId())->flush();

            $folders = $folderRepo->findAllFoldersForUser($recipient->getId());
        }

        //
        // Check quotas.
        //
        $used = $folderManager->checkQuotaAllowanceUsed($folders);

        if ($used >= $quota) {
            $this->container->get('session')->setFlash('notice',
                $this->container->get('translator')->trans('ccdn_message_message.flash.message.send.inbox_full', array('%user%' => $recipient->getUsername()), 'CCDNMessageMessageBundle'));

            continue;
        } else {
            if ($recipient) {
                if ($recipient->getId() != $sender->getId()) {
                    $this->container->get('session')->setFlash('notice',
                        $this->container->get('translator')->trans('ccdn_message_message.flash.message.sent.success', array('%user%' => $recipient->getUsername()), 'CCDNMessageMessageBundle'));
                }
            }
        }

        //
        // Create the message.
        //
        $temp = new Message();
        $temp->setSendTo($message->getSendTo());
        $temp->setSentTo(($recipient) ? $recipient : null);
        $temp->setSentFrom($sender);
        $temp->setSubject($message->getSubject());
        $temp->setBody($message->getBody());
        $temp->setCreatedDate($message->getCreatedDate());
        $temp->setSentDate(new \DateTime());
        $temp->setIsDraft($message->getIsDraft());
        $temp->setIsFlagged($message->getIsFlagged());
        //$temp->setAttachment($message->getAttachment());

        if ($isCarbonCopy) {
            $temp->setOwnedBy($sender);
            $temp->setFolder($folders[1]);
            $temp->setIsRead(true);
        } else {
            $temp->setOwnedBy($recipient);
            $temp->setFolder($folders[0]);
            $temp->setIsRead(false);
        }

        $this->persist($temp);

        return $this;
    }

    /**
     *
     * @access public
     * @param Message $message
     * @return self
     */
    public function insert($message)
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

            $sendToUsers = $this->container->get('ccdn_user_user.repository.user')->findTheseUsersByUsername($recipients);
        } else {
            $recipients = array($value);

            $sendToUsers = $this->container->get('ccdn_user_user.repository.user')->findByUsername($recipients);
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        //
        // Send.
        //
        foreach ($sendToUsers as $recipient_key => $recipient) {
            $this->sendTo($message, $recipient, $user, false);
        }

        // add Carbon Copy.
        $this->sendTo($message, null, $user, true);

        $this->flush();

        //
        // Update recipients folders read/unread cache counts.
        //
        foreach ($sendToUsers as $recipient) {
            $this->updateAllFolderCachesForUser($recipient);
        }

        return $this;
    }

    /**
     *
     * @access public
     * @param Message $message
     * @return self
     */
    public function markAsRead($message)
    {
        $message->setIsRead(true);
        $this->persist($message)->flush();

        return $this;
    }

    /**
     *
     * @access public
     * @param Message $message
     * @return self
     */
    public function markAsUnread($message)
    {
        $message->setIsRead(false);
        $this->persist($message)->flush();

        return $this;
    }

    /**
     *
     * @access public
     * @param Message $message, array $folders
     * @return self
     */
    public function delete($message, $folders)
    {

        if ($message->getFolder()->getName() == 'trash') {
            $this->remove($message);
        } else {
            foreach ($folders as $folder) {
                if ($folder->getName() == 'trash') {
                    $message->setFolder($folder);

                    break;
                }
            }

            $this->persist($message);
        }

        return $this;
    }

    /**
     *
     * @access public
     * @param array $messages, array $folders
     * @return self
     */
    public function bulkDelete($messages, $folders)
    {
        // find the trash folder
        foreach ($folders as $folder) {
            if ($folder->getName() == 'trash') {
                $trash = $folder;

                break;
            }
        }

        // trash or remove each message
        foreach ($messages as $message) {
            if ($message->getFolder()->getName() == 'trash') {
                $this->remove($message);
            } else {
                $message->setFolder($trash);
                $this->persist($message);
            }
        }

        return $this;
    }

    /**
     *
     * @access public
     * @param array $messages
     * @return self
     */
    public function bulkMarkAsRead($messages)
    {
        foreach ($messages as $message) {
            $message->setIsRead(true);
            $this->persist($message);
        }

        return $this;
    }

    /**
     *
     * @access public
     * @param array $messages
     * @return self
     */
    public function bulkMarkAsUnread($messages)
    {
        foreach ($messages as $message) {
            $message->setIsRead(false);
            $this->persist($message);
        }

        return $this;
    }

    /**
     *
     * @access public
     * @param array $messages, Folder $moveTo
     * @return self
     */
    public function bulkMoveToFolder($messages, $moveTo)
    {

        foreach ($messages as $message) {
            $message->setFolder($moveTo);
            $this->persist($message);
        }

        return $this;
    }

    /**
     *
     * @access public
     * @param $user
     * @return self
     */
    public function updateAllFolderCachesForUser($user)
    {
        $folders = $this->container->get('ccdn_message_message.repository.folder')->findAllFoldersForUser($user->getId());

        $folderManager = $this->container->get('ccdn_message_message.manager.folder');

        foreach ($folders as $folder) {
            $folderManager->updateFolderCounterCaches($folder);
        }

        $folderManager->flush();

        $this->container->get('ccdn_message_message.manager.registry')->updateCacheUnreadMessagesForUser($user);

        return $this;
    }

}
