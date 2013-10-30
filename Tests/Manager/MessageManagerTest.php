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

class MessageManagerTest extends TestBase
{
	public function testSaveMessage()
	{
		$this->purge();
		
		$user = $this->addNewUser('bob', 'bob@foo.com', 'root');
		$folders = $this->addFixturesForFolders(array($user));

		$message = $this->addNewMessage('subject', 'message body', $user, false, false);
		$this->getMessageModel()->saveMessage($message);

		$envelopes = $this->addFixturesForEnvelopes(array($message), $folders, array($user));
		$messageFound = $this->getMessageModel()->getAllEnvelopesForMessageById($message->getId());
		
		$this->assertNotNull($messageFound);
	}
}
