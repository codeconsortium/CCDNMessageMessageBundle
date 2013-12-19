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

class MessageRepositoryTest extends TestBase
{
    public function testGetAllEnvelopesForMessageById()
	{
		$this->purge();
		$user = $this->addNewUser('bob', 'bob@foo.com', 'root');
		$folders = $this->addFixturesForFolders(array($user));
		$message = $this->addNewMessage('subject', 'body', $user);
		$this->addFixturesForEnvelopes(array($message), $folders, array($user));
		$messageFound = $this->getMessageModel()->getAllEnvelopesForMessageById($message->getId());
		
		$this->assertNotNull($messageFound);
		$this->assertCount(5, $messageFound->getEnvelopes());
	}
}