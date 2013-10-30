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

class EnvelopeRepositoryTest extends TestBase
{
    public function testFindEnvelopeByIdForUser()
    {
		$this->purge();
		
		$user = $this->addNewUser('bob', 'bob@foo.com', 'root');
		$folders = $this->addFixturesForFolders(array($user));
		$messages = $this->addFixturesForMessages(array($user));
		$envelopes = $this->addFixturesForEnvelopes($messages, $folders, array($user));
		
		$envelopeFound = $this->getEnvelopeModel()->findEnvelopeByIdForUser($envelopes[0]->getId(), $user->getId());
		
		$this->assertNotNull($envelopeFound);
		$this->assertInstanceOf('CCDNMessage\MessageBundle\Entity\Envelope', $envelopeFound);
    }

    public function testFindAllEnvelopesForFolderByIdPaginated()
    {
		$this->purge();
		
		$user = $this->addNewUser('bob', 'bob@foo.com', 'root');
		$folders = $this->addFixturesForFolders(array($user));
		$messages = $this->addFixturesForMessages(array($user));
		$envelopes = $this->addFixturesForEnvelopes($messages, $folders, array($user));
		
		$pager = $this->getEnvelopeModel()->findAllEnvelopesForFolderByIdPaginated($folders[0]->getId(), $user->getId(), 1, 25);
		
		$envelopesFound = $pager->getItems();
		$this->assertCount(3, $envelopesFound);
    }

    public function testFindTheseEnvelopesByIdAndByUserId()
    {
		$this->purge();
		
		$user = $this->addNewUser('bob', 'bob@foo.com', 'root');
		$folders = $this->addFixturesForFolders(array($user));
		$messages = $this->addFixturesForMessages(array($user));
		$envelopes = $this->addFixturesForEnvelopes($messages, $folders, array($user));
		
		$envelopesFound = $this->getEnvelopeModel()->findTheseEnvelopesByIdAndByUserId(array($envelopes[0]->getId(), $envelopes[1]->getId(), $envelopes[2]->getId()), $user->getId());
		$this->assertCount(3, $envelopesFound);
    }

    public function testGetReadCounterForFolderById()
    {
		$this->purge();
		
		$user = $this->addNewUser('bob', 'bob@foo.com', 'root');
		$folders = $this->addFixturesForFolders(array($user));
		$messages = $this->addFixturesForMessages(array($user));
		$envelopes = $this->addFixturesForEnvelopes($messages, $folders, array($user));
		
		$counter = $this->getEnvelopeModel()->getReadCounterForFolderById($folders[0]->getId(), $user->getId());
		
		$this->assertTrue(array_key_exists('read', $counter));
		$this->assertSame('0', $counter['read']);
    }

    public function testGetUnreadCounterForFolderById()
    {
		$this->purge();
		
		$user = $this->addNewUser('bob', 'bob@foo.com', 'root');
		$folders = $this->addFixturesForFolders(array($user));
		$messages = $this->addFixturesForMessages(array($user));
		$envelopes = $this->addFixturesForEnvelopes($messages, $folders, array($user));
		
		$counter = $this->getEnvelopeModel()->getUnreadCounterForFolderById($folders[0]->getId(), $user->getId());

		$this->assertTrue(array_key_exists('unread', $counter));
		$this->assertSame('3', $counter['unread']);
    }
}