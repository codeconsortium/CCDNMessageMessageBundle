<?php
namespace CCDNMessage\MessageBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use CCDNUser\UserBundle\Entity\User;

use CCDNMessage\MessageBundle\Entity\Folder;
use CCDNMessage\MessageBundle\Entity\Message;

class MessageRepositoryFunctionalTest extends WebTestCase
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
    public function testFindAllPaginatedForFolderById()
    {
		$user 		= $this->addUser();
		$folder 	= $this->addFolder($user);
		$message 	= $this->addMessage($folder, $user);
		
		$folderId 	= $folder->getId();
		$userId 	= $user->getId();
		
        $results = $this->em
            ->getRepository('CCDNMessageMessageBundle:Message')
            ->findAllPaginatedForFolderById($folderId, $userId);

		$results->setMaxPerPage(5);
		$results->setCurrentPage(1, false, true);
		
        $this->assertCount(1, $results->getCurrentPageResults());
    }



	/**
	 *
	 * @access public
	 */
	public function testFindMessageByIdForUser()
	{
		$user 		= $this->addUser();
		$folder 	= $this->addFolder($user);
		$message 	= $this->addMessage($folder, $user);
		
		$messageId 	= $message->getId();
		$userId 	= $user->getId();
		
        $result = $this->em
            ->getRepository('CCDNMessageMessageBundle:Message')
            ->findMessageByIdForUser($messageId, $userId);

        $this->assertTrue(((is_object($result) && ($result instanceof Message)) ? true : false));
	}
	


	/**
	 *
	 * @access public
	 */
	public function testFindTheseMessagesByUserId()
	{
		$user 		= $this->addUser();
		$folder 	= $this->addFolder($user);
		$message1 	= $this->addMessage($folder, $user);
		$message2 	= $this->addMessage($folder, $user);
		$message3 	= $this->addMessage($folder, $user);
		
		$userId 	= $user->getId();
		$messageIds	= array(
			$message1->getId(),
			$message2->getId(),
			$message3->getId(),
		);
		
        $results = $this->em
            ->getRepository('CCDNMessageMessageBundle:Message')
            ->findTheseMessagesByUserId($messageIds, $userId);

        $this->assertCount(3, $results);
	}
	
}