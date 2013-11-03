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

namespace CCDNMessage\MessageBundle\Tests\Repository;

use CCDNMessage\MessageBundle\Tests\TestBase;
use CCDNMessage\MessageBundle\Entity\Folder;

class EnvelopeManagerTest extends TestBase
{
	public function testSaveEnvelope()
	{
		$this->purge();
		
		$user = $this->addNewUser('bob', 'bob@foo.com', 'root', true, true);
		$folders = $this->addFixturesForFolders(array($user));
		$messages = $this->addFixturesForMessages(array($user));
		$envelope = $this->addNewEnvelope($messages[0], $folders[0], $user, $user, false, false, true, false, false);
		
		$this->getEnvelopeModel()->saveEnvelope($envelope);
		
		$envelopeFound = $this->getEnvelopeModel()->findEnvelopeByIdForUser($envelope->getId(), $user->getId());
		
		$this->assertNotNull($envelopeFound);
	}

    public function testMarkAsRead()
    {
		$this->purge();
		
		$user = $this->addNewUser('bob', 'bob@foo.com', 'root', true, true);
		$folders = $this->addFixturesForFolders(array($user));
		$messages = $this->addFixturesForMessages(array($user));
		$envelopes = $this->addFixturesForEnvelopes(array($messages[0]), $folders, array($user));
		
		$this->getEnvelopeModel()->markAsRead($envelopes[0], $folders);
		
		$envelopeFound = $this->getEnvelopeModel()->findEnvelopeByIdForUser($envelopes[0]->getId(), $user->getId());
		
		$this->assertTrue($envelopeFound->isRead());
    }

    public function testBulkMarkAsRead()
    {
		$this->purge();
		
		$user = $this->addNewUser('bob', 'bob@foo.com', 'root', true, true);
		$folders = $this->addFixturesForFolders(array($user));
		$messages = $this->addFixturesForMessages(array($user));
		$envelopes = $this->addFixturesForEnvelopes(array($messages[0]), $folders, array($user));
		
		$this->getEnvelopeModel()->bulkMarkAsRead($envelopes, $folders);
		
		$ids = array();
		foreach ($envelopes as $envelope) {
			$ids[] = $envelope->getId();
		}
		
		$envelopesFound = $this->getEnvelopeModel()->findTheseEnvelopesByIdAndByUserId($ids, $user->getId());
		
		foreach ($envelopesFound as $envelopeFound) {
			$this->assertTrue($envelopeFound->isRead());
		}
    }

    public function testMarkAsUnread()
    {
		$this->purge();
		
		$user = $this->addNewUser('bob', 'bob@foo.com', 'root', true, true);
		$folders = $this->addFixturesForFolders(array($user));
		$messages = $this->addFixturesForMessages(array($user));
		$envelopes = $this->addFixturesForEnvelopes(array($messages[0]), $folders, array($user));
		
		$this->getEnvelopeModel()->markAsUnread($envelopes[0], $folders);
		
		$envelopeFound = $this->getEnvelopeModel()->findEnvelopeByIdForUser($envelopes[0]->getId(), $user->getId());
		
		$this->assertFalse($envelopeFound->isRead());
    }

    public function testBulkMarkAsUnread()
    {
		$this->purge();
		
		$user = $this->addNewUser('bob', 'bob@foo.com', 'root', true, true);
		$folders = $this->addFixturesForFolders(array($user));
		$messages = $this->addFixturesForMessages(array($user));
		$envelopes = $this->addFixturesForEnvelopes(array($messages[0]), $folders, array($user));
		
		$this->getEnvelopeModel()->bulkMarkAsUnread($envelopes, $folders);
		
		$ids = array();
		foreach ($envelopes as $envelope) {
			$ids[] = $envelope->getId();
		}
		
		$envelopesFound = $this->getEnvelopeModel()->findTheseEnvelopesByIdAndByUserId($ids, $user->getId());
		
		foreach ($envelopesFound as $envelopeFound) {
			$this->assertFalse($envelopeFound->isRead());
		}
    }

    public function testDelete()
    {
		$this->purge();
		
		$user = $this->addNewUser('bob', 'bob@foo.com', 'root', true, true);
		$folders = $this->addFixturesForFolders(array($user));
		$messages = $this->addFixturesForMessages(array($user));
		$envelopes = $this->addFixturesForEnvelopes($messages, $folders, array($user));
		
		$this->getEnvelopeModel()->delete($envelopes[0], $folders);
		
		$envelopeFound = $this->getEnvelopeModel()->findEnvelopeByIdForUser($envelopes[0]->getId(), $user->getId());
		
		$this->assertSame(Folder::SPECIAL_TYPE_TRASH, $envelopeFound->getFolder()->getSpecialType());
    }

    public function testBulkDelete()
    {
		$this->purge();
		
		$user = $this->addNewUser('bob', 'bob@foo.com', 'root', true, true);
		$folders = $this->addFixturesForFolders(array($user));
		$messages = $this->addFixturesForMessages(array($user));
		$envelopes = $this->addFixturesForEnvelopes($messages, $folders, array($user));
		
		$this->getEnvelopeModel()->bulkDelete($envelopes, $folders, $user);
		
		$ids = array();
		foreach ($envelopes as $envelope) {
			$ids[] = $envelope->getId();
		}
		
		$envelopesFound = $this->getEnvelopeModel()->findTheseEnvelopesByIdAndByUserId($ids, $user->getId());
		
		$this->assertNotNull($envelopesFound);
    }

    public function testBulkMoveToFolder()
    {
		$this->purge();
		
		$user = $this->addNewUser('bob', 'bob@foo.com', 'root', true, true);
		$folders = $this->addFixturesForFolders(array($user));
		$messages = $this->addFixturesForMessages(array($user));
		$envelopes = $this->addFixturesForEnvelopes($messages, $folders, array($user));
		
		$this->getEnvelopeModel()->bulkMoveToFolder($envelopes, $folders[2]);
		
		$ids = array();
		foreach ($envelopes as $envelope) {
			$ids[] = $envelope->getId();
		}
		
		$envelopesFound = $this->getEnvelopeModel()->findTheseEnvelopesByIdAndByUserId($ids, $user->getId());

		$this->assertNotNull($envelopesFound);
		$this->assertCount(count($ids), $envelopesFound);

		foreach ($envelopesFound as $found) {
			$this->assertSame($found->getFolder()->getId(), $folders[2]->getId());
		}
    }
}
