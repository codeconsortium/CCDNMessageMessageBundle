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

namespace CCDNMessage\MessageBundle\Model\Component\Manager;

use Symfony\Component\Security\Core\User\UserInterface;

use CCDNMessage\MessageBundle\Model\Component\Manager\ManagerInterface;
use CCDNMessage\MessageBundle\Model\Component\Manager\BaseManager;

use CCDNMessage\MessageBundle\Entity\Folder;
use CCDNMessage\MessageBundle\Entity\Envelope;

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

    /**
     *
     * @access public
     * @return \CCDNMessage\MessageBundle\Entity\Envelope
     */
    public function createEnvelope()
    {
        return $this->gateway->createEnvelope();
    }

    public function saveEnvelope(Envelope $envelope)
    {
        $this->persist($envelope);
        $this->flush();
        $this->refresh($envelope);

        return $envelope;
    }

    /**
     *
     * @access public
     * @param  \CCDNMessage\MessageBundle\Entity\Envelope                         $envelope
     * @return \CCDNMessage\MessageBundle\Model\Component\Manager\EnvelopeManager
     */
    public function markAsRead(Envelope $envelope)
    {
        $envelope->setRead(true);
        $this
            ->persist($envelope)
            ->flush()
        ;

        return $this;
    }

    /**
     *
     * @access public
     * @param  array                                                              $envelopes
     * @return \CCDNMessage\MessageBundle\Model\Component\Manager\EnvelopeManager
     */
    public function bulkMarkAsRead($envelopes)
    {
        foreach ($envelopes as $envelope) {
            $envelope->setRead(true);
            $this->persist($envelope);
        }

        $this->flush();

        return $this;
    }

    /**
     *
     * @access public
     * @param  \CCDNMessage\MessageBundle\Entity\Envelope                         $envelope
     * @return \CCDNMessage\MessageBundle\Model\Component\Manager\EnvelopeManager
     */
    public function markAsUnread(Envelope $envelope)
    {
        $envelope->setRead(false);
        $this
            ->persist($envelope)
            ->flush()
        ;

        return $this;
    }

    /**
     *
     * @access public
     * @param  array                                                              $envelopes
     * @return \CCDNMessage\MessageBundle\Model\Component\Manager\EnvelopeManager
     */
    public function bulkMarkAsUnread($envelopes)
    {
        foreach ($envelopes as $envelope) {
            $envelope->setRead(false);
            $this->persist($envelope);
        }

        $this->flush();

        return $this;
    }

    /**
     *
     * @access public
     * @param  \CCDNMessage\MessageBundle\Entity\Envelope                         $envelope,
     * @return \CCDNMessage\MessageBundle\Model\Component\Manager\EnvelopeManager
     */
    protected function hardDelete(Envelope $envelope)
    {
        $message = $envelope->getMessage();

        if (count($message->getEnvelopes()) < 2) {
            if (count($message->getThread()->getMessages()) < 2) {
                $this->remove($envelope->getThread());
            }

            $this->remove($envelope->getMessage());
        }

        $this->remove($envelope);
    }

    /**
     *
     * @access public
     * @param  \CCDNMessage\MessageBundle\Entity\Envelope                         $envelope,
     * @param  array                                                              $folders
     * @return \CCDNMessage\MessageBundle\Model\Component\Manager\EnvelopeManager
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

        return $this;
    }

    /**
     *
     * @access public
     * @param  array                                                              $envelopes
     * @param  array                                                              $folders
     * @return \CCDNMessage\MessageBundle\Model\Component\Manager\EnvelopeManager
     */
    public function bulkDelete($envelopes, $folders)
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

        return $this;
    }

    /**
     *
     * @access public
     * @param  array                                                              $envelopes
     * @param  \CCDNMessage\MessageBundle\Entity\Folder                           $moveTo
     * @return \CCDNMessage\MessageBundle\Model\Component\Manager\EnvelopeManager
     */
    public function bulkMoveToFolder($envelopes, Folder $moveTo)
    {
        foreach ($envelopes as $envelope) {
            $envelope->setFolder($moveTo);
            $this->persist($envelope);
        }

        $this->flush();

        return $this;
    }
}
