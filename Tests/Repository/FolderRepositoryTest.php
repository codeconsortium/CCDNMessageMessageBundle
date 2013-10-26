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

class FolderRepositoryTest extends TestBase
{
    public function testFindAllFoldersForUserById()
	{
		$this->purge();
		
		$users = $this->addFixturesForUsers();
		$folders = $this->addFixturesForFolders($users);

		$foldersFound = $this->getFolderModel()->findAllFoldersForUserById($users['harry']->getId());
		
		$this->assertCount(5, $foldersFound);
	}
}