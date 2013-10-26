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

class ThreadRepositoryTest extends TestBase
{
    public function testFindThreadWithAllEnvelopesByThreadIdAndUserId()
	{
		$this->purge();
		
		$user = $this->addNewUser('bob', 'bob@foo.com', 'root');
		$folders = $this->addFixturesForFolders(array($user));
		$messages = $this->addFixturesForMessages(array($user));
		$envelopes = $this->addFixturesForEnvelopes($messages, $folders, array($user));

		$threadFound = $this->getThreadModel()->findThreadWithAllEnvelopesByThreadIdAndUserId($messages[0]->getThread()->getId(), $user->getId());

		$this->assertNotNull($threadFound);
		$this->assertInstanceOf('CCDNMessage\MessageBundle\Entity\Thread', $threadFound);
		$this->assertCount(5, $threadFound->getEnvelopes());
	}
}