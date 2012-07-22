<?php
namespace CCDNMessage\MessageBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use CCDNUser\UserBundle\Entity\User;

use CCDNMessage\MessageBundle\Entity\Folder;
use CCDNMessage\MessageBundle\Entity\Message;

class FolderRepositoryFunctionalTest extends WebTestCase
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
	private function addFolder($user)
	{
		// Create a Folder
		$folder = new Folder();
		
		$folder->setName('inbox');
		$folder->setOwnedBy($user);

		$this->em->persist($folder);
		$this->em->flush();		
		$this->em->refresh($folder);
		
		return $folder;
	}
	


	/**
	 *
	 * @access private
	 */
	private function addMessage($folder, $user)
	{
		// Create a Message
		$message = new Message();

		$message->setFolder($folder);
		$message->setSendTo($user->getUsername());
		$message->setOwnedBy($user);
		$message->setSentTo($user);
		$message->setSubject(rand());
		$message->setBody(rand());
		$message->setIsDraft(false);
		$message->setIsRead(false);
		$message->setIsFlagged(false);
		
		$this->em->persist($message);
		$this->em->flush($message);
		$this->em->refresh($message);
		
		return $message;
	}
	


	/**
	 *
	 * @access public
	 */
    public function testFindAllFoldersForUser()
    {
		$user 		= $this->addUser();
		$folder1 	= $this->addFolder($user);
		$folder2 	= $this->addFolder($user);
		$folder3 	= $this->addFolder($user);
		
		$userId 	= $user->getId();
		
        $results = $this->em
            ->getRepository('CCDNMessageMessageBundle:Folder')
            ->findAllFoldersForUser($userId);

        $this->assertCount(3, $results);
    }



	/**
	 *
	 * @access public
	 */
	public function testGetReadCounterForFolderById()
	{
		$user 		= $this->addUser();
		$folder 	= $this->addFolder($user);
		$message1 	= $this->addMessage($folder, $user);
		$message2 	= $this->addMessage($folder, $user);
		$message3	= $this->addMessage($folder, $user);
		$message4 	= $this->addMessage($folder, $user);
		
		$message1->setIsRead(true);
		$message2->setIsRead(true);
		$message3->setIsRead(false);
		$message4->setIsRead(false);
		
		$this->em->persist($message1, $message2, $message3, $message4);
		$this->em->flush();
		
		$userId 	= $user->getId();
		$folderId 	= $folder->getId();
		
        $result = $this->em
            ->getRepository('CCDNMessageMessageBundle:Folder')
            ->getReadCounterForFolderById($folderId);

        $this->assertEquals(2, $result['readCount']);
	}
	


	/**
	 *
	 * @access public
	 */
	public function testGetUnreadCounterForFolderById()
	{
		$user 		= $this->addUser();
		$folder 	= $this->addFolder($user);
		$message1 	= $this->addMessage($folder, $user);
		$message2 	= $this->addMessage($folder, $user);
		$message3	= $this->addMessage($folder, $user);
		$message4 	= $this->addMessage($folder, $user);
		
		$message1->setIsRead(true);
		$message2->setIsRead(true);
		$message3->setIsRead(false);
		$message4->setIsRead(false);
		
		$this->em->persist($message1, $message2, $message3, $message4);
		$this->em->flush();
		
		$userId 	= $user->getId();
		$folderId 	= $folder->getId();
		
        $result = $this->em
            ->getRepository('CCDNMessageMessageBundle:Folder')
            ->getUnreadCounterForFolderById($folderId);

        $this->assertEquals(2, $result['unreadCount']);
	}
	
}