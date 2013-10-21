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

namespace CCDNMessage\MessageBundle\Model\Manager;

use Symfony\Component\Security\Core\User\UserInterface;

use CCDNMessage\MessageBundle\Model\Manager\ManagerInterface;
use CCDNMessage\MessageBundle\Model\Manager\BaseManager;

use CCDNMessage\MessageBundle\Entity\Folder;
use CCDNMessage\MessageBundle\Entity\Envelope;
use CCDNMessage\MessageBundle\Entity\Message;
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
class EnvelopeManager extends BaseManager implements ManagerInterface
{
    const MESSAGE_SEND = 0;
    const MESSAGE_SAVE_CARBON_COPY = 1;
    const MESSAGE_SAVE_DRAFT = 2;

    private $sendMode = array(
        self::MESSAGE_SEND,
        self::MESSAGE_SAVE_CARBON_COPY,
        self::MESSAGE_SAVE_DRAFT,
    );

	public function saveEnvelope(Envelope $envelope)
	{
		return $this->persist($envelope)->flush();
	}

    /**
     *
     * @access public
     * @param  \CCDNMessage\MessageBundle\Entity\Envelope               $envelope
     * @param  array                                                    $folders
     * @return \CCDNMessage\MessageBundle\Model\Manager\EnvelopeManager
     */
    public function markAsRead(Envelope $envelope, $folders)
    {
        $envelope->setIsRead(true);
        $this
            ->persist($envelope)
            ->flush()
        ;

        //$this->managerBag->getFolderManager()->updateAllFolderCachesForUser($envelope->getOwnedByUser(), $folders)->flush();

        return $this;
    }

    /**
     *
     * @access public
     * @param  array                                                    $envelopes
     * @param  array                                                    $folders
     * @param  \Symfony\Component\Security\Core\User\UserInterface      $user
     * @return \CCDNMessage\MessageBundle\Model\Manager\EnvelopeManager
     */
    public function bulkMarkAsRead($envelopes, $folders, UserInterface $user)
    {
        foreach ($envelopes as $envelope) {
            $envelope->setIsRead(true);

            $this->persist($envelope);
        }

        $this->flush();

        //$this->managerBag->getFolderManager()->updateAllFolderCachesForUser($user, $folders)->flush();

        return $this;
    }

    /**
     *
     * @access public
     * @param  Envelope                                                 $envelope
     * @param  array                                                    $folders
     * @return \CCDNMessage\MessageBundle\Model\Manager\EnvelopeManager
     */
    public function markAsUnread(Envelope $envelope, $folders)
    {
        $envelope->setIsRead(false);
        $this
            ->persist($envelope)
            ->flush()
        ;

        //$this->managerBag->getFolderManager()->updateAllFolderCachesForUser($envelope->getOwnedByUser(), $folders)->flush();

        return $this;
    }

    /**
     *
     * @access public
     * @param  array                                                    $envelopes
     * @param  array                                                    $folders
     * @param  \Symfony\Component\Security\Core\User\UserInterface      $user
     * @return \CCDNMessage\MessageBundle\Model\Manager\EnvelopeManager
     */
    public function bulkMarkAsUnread($envelopes, $folders, UserInterface $user)
    {
        foreach ($envelopes as $envelope) {
            $envelope->setIsRead(false);
            $this->persist($envelope);
        }

        $this->flush();

        //$this->managerBag->getFolderManager()->updateAllFolderCachesForUser($user, $folders)->flush();

        return $this;
    }

    /**
     *
     * @access public
     * @param  \CCDNMessage\MessageBundle\Entity\Envelope               $envelope,
     * @return \CCDNMessage\MessageBundle\Model\Manager\EnvelopeManager
     */
    protected function hardDelete(Envelope $envelope)
    {
        //$message = $this->managerBag->getMessageManager()->getAllEnvelopesForMessageById($envelope->getMessage()->getId());
        //
        //if (count($message->getEnvelopes()) < 2) {
        //    if (count($message->getThread()->getMessages()) < 2) {
        //        $this->remove($envelope->getThread());
        //    }
        //
        //    $this->remove($envelope->getMessage());
        //}
        //
        //$this->remove($envelope);
    }

    /**
     *
     * @access public
     * @param  \CCDNMessage\MessageBundle\Entity\Envelope               $envelope,
     * @param  array                                                    $folders
     * @return \CCDNMessage\MessageBundle\Model\Manager\EnvelopeManager
     */
    public function delete(Envelope $envelope, $folders)
    {
        if ($envelope->getFolder()->getName() == 'trash') {
            $this->hardDelete($envelope);
        } else {
            foreach ($folders as $folder) {
                if ($folder->getName() == 'trash') {
                    $envelope->setFolder($folder);
        
                    break;
                }
            }
        
            $this->persist($envelope);
        }
        
        $this->flush();
        
        //$this->managerBag->getFolderManager()->updateAllFolderCachesForUser($envelope->getOwnedByUser(), $folders)->flush();
        
        return $this;
    }

    /**
     *
     * @access public
     * @param  array                                                    $envelopes
     * @param  array                                                    $folders
     * @param  \Symfony\Component\Security\Core\User\UserInterfaces     $user
     * @return \CCDNMessage\MessageBundle\Model\Manager\EnvelopeManager
     */
    public function bulkDelete($envelopes, $folders, UserInterface $user)
    {
        // find the trash folder
        foreach ($folders as $folder) {
            if ($folder->getName() == 'trash') {
                $trash = $folder;
        
                break;
            }
        }
        
        // trash or remove each message
        foreach ($envelopes as $envelope) {
            if ($envelope->getFolder()->getName() == 'trash') {
                $this->hardDelete($envelope);
            } else {
                $envelope->setFolder($trash);
        
                $this->persist($envelope);
            }
        }
        
        $this->flush();
        
        //$this->managerBag->getFolderManager()->updateAllFolderCachesForUser($user, $folders)->flush();
        
        return $this;
    }

    /**
     *
     * @access public
     * @param  array                                                    $envelopes
     * @param  array                                                    $folders
     * @param  \CCDNMessage\MessageBundle\Entity\Folder                 $moveTo
     * @param  \Symfony\Component\Core\User\UserInterface               $user
     * @return \CCDNMessage\MessageBundle\Model\Manager\EnvelopeManager
     */
    public function bulkMoveToFolder($envelopes, $folders, Folder $moveTo, UserInterface $user)
    {
        foreach ($envelopes as $envelope) {
            $envelope->setFolder($moveTo);
            $this->persist($envelope);
        }
        
        $this->flush();
        
        //$this->managerBag->getFolderManager()->updateAllFolderCachesForUser($user, $folders)->flush();
        
        return $this;
    }
}
