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

class UserRepositoryTest extends TestBase
{
    public function testFindOneUserById()
	{
		$this->purge();
		
		$users = $this->addFixturesForUsers();

		$userFound = $this->getUserModel()->findOneUserById($users['tom']->getId());
		
		$this->assertNotNull($userFound);
		$this->assertInstanceOf('Symfony\Component\Security\Core\User\UserInterface', $userFound);
	}

    public function testFindTheseUsersByUsername()
	{
		$this->purge();
		
		$users = $this->addFixturesForUsers();
		$usernames = array($users['tom']->getUsername(), $users['harry']->getUsername());
		
		$usersFound = $this->getUserModel()->findTheseUsersByUsername($usernames);
		
		$this->assertNotNull($usersFound);
		$this->assertCount(2, $usersFound);
		$this->assertInstanceOf('Symfony\Component\Security\Core\User\UserInterface', $usersFound[0]);
	}
}