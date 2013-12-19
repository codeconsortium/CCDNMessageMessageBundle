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

class RegistryRepositoryTest extends TestBase
{
    public function testFindRegistryForUserById()
	{
		$this->purge();
		$user = $this->addNewUser('bob', 'bob@foo.com', 'root');
		$this->addNewRegistry($user);
		$registryFound = $this->getRegistryModel()->findOneRegistryForUserById($user->getId());

		$this->assertNotNull($registryFound);
		$this->assertInstanceOf('CCDNMessage\MessageBundle\Entity\Registry', $registryFound);
	}
}