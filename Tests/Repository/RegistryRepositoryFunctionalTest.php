<?php
namespace CCDNMessage\MessageBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use CCDNUser\UserBundle\Entity\User;

use CCDNMessage\MessageBundle\Entity\Registry;

class RegistryRepositoryFunctionalTest extends WebTestCase
{
	
	
	
    /**
	 *
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;



	/**
	 *
	 * @access public
	 */
    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
    }



	/**
	 *
	 * @access private
	 */
	private function addUser()
	{
		// Create a User
		$user = new User();

		$user->setUsername(rand());
		$user->setPassword('foo');
		$user->setEmail(rand());

		$this->em->persist($user);
		$this->em->flush();
		$this->em->refresh($user);
		
		return $user;
	}
	


	/**
	 *
	 * @access private
	 */
	private function addRegistry($user)
	{
		// Create a Folder
		$registry = new Registry();
		
		$registry->setOwnedBy($user);
		$registry->setCachedUnreadMessagesCount(0);

		$this->em->persist($registry);
		$this->em->flush();		
		$this->em->refresh($registry);
		
		return $registry;
	}
	


	/**
	 *
	 * @access public
	 */
    public function testFindRegistryRecordForUser()
    {
		$user 		= $this->addUser();
		$registry	= $this->addRegistry($user);
		
		$userId 	= $user->getId();
		
        $result = $this->em
            ->getRepository('CCDNMessageMessageBundle:Registry')
            ->findRegistryRecordForUser($userId);
	
        $this->assertTrue(((is_object($result) && ($result instanceof Registry)) ? true : false));
    }

}