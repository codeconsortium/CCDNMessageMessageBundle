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

namespace CCDNMessage\MessageBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;

use CCDNMessage\MessageBundle\Entity\Folder;
use CCDNMessage\MessageBundle\Entity\Message;
use CCDNMessage\MessageBundle\Entity\Envelope;
use CCDNMessage\MessageBundle\Entity\Thread;
use CCDNMessage\MessageBundle\Entity\Registry;

use CCDNMessage\MessageBundle\Tests\Functional\src\Entity\User;
//use CCDNUser\UserBundle\Entity\User;

class TestBase extends WebTestCase
{
    /**
	 *
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

	/**
	 *
	 * @var $container
	 */
	private $container;

	/**
	 *
	 * @access public
	 */
    public function setUp()
    {
        $kernel = static::createKernel();

        $kernel->boot();
		
		$this->container = $kernel->getContainer();

        $this->em = $this->container->get('doctrine.orm.entity_manager');
		
		$this->purge();
    }

	/*
     *
     * Close doctrine connections to avoid having a 'too many connections'
     * message when running many tests
     */
	public function tearDown(){
		$this->container->get('doctrine')->getConnection()->close();
	
		parent::tearDown();
	}

    protected function purge()
    {
        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();
	}

	protected function addNewUser($username, $email, $password, $persist = true, $andFlush = true)
	{
		$user = new User();
		
		$user->setUsername($username);
		$user->setEmail($email);
		$user->setPlainPassword($password);
		
		if ($persist) {
			$this->em->persist($user);
		
			if ($andFlush) {
				$this->em->flush();
				$this->em->refresh($user);
			}
		}
		
		return $user;
	}

	protected function addFixturesForUsers()
	{
		$users = array();
		
		$userNames = array('admin', 'tom', 'dick', 'harry');
		foreach ($userNames as $username) {
			$users[$username] = $this->addNewUser($username, $username . '@foobar.com', 'password', true, false);
		}
		
		$this->em->flush();
		
		return $users;
	}

	protected function addNewFolder($folderName, $ownedByUser, $specialType, $persist = true, $andFlush = true)
	{
		$folder = new Folder();
		
		$folder->setName($folderName);
		$folder->setOwnedByUser($ownedByUser);
		$folder->setSpecialType($specialType);
		
		if ($persist) {
			$this->em->persist($folder);
		
			if ($andFlush) {
				$this->em->flush();
				$this->em->refresh($folder);
			}
		}
		
		return $folder;
	}

	protected function addFixturesForFolders($users)
	{
		$folders = array();

		$folderNames = Folder::$defaultSpecialTypes;
		foreach ($users as $ownedByUser) {
			foreach ($folderNames as $folderSpecialType => $folderName) {
				$folders[] = $this->addNewFolder($folderName, $ownedByUser, $folderSpecialType, true, false);
			}
		}
		
		$this->em->flush();
		
		return $folders;
	}

	protected function addNewMessage($messageSubject, $messageBody, $sentFromUser, $persist = true, $andFlush = true)
	{
		$message = new Message();
		
		$message->setSentFromUser($sentFromUser);
		$message->setSubject($messageSubject);
		$message->setBody($messageBody);
		$message->setCreatedDate(new \Datetime('now'));
		$message->setSendTo(' ');
		$message->setThread(new Thread());
		
		if ($persist) {
			$this->em->persist($message);
		
			if ($andFlush) {
				$this->em->flush();
				$this->em->refresh($message);
			}
		}
		
		return $message;
	}

	protected function addFixturesForMessages($users)
	{
		$messages = array();
		
		$messageBodies = array('test_category_1', 'test_category_2', 'test_category_3');
		foreach ($users as $sentFromUser) {
			foreach ($messageBodies as $messageBody) {
				$messages[] = $this->addNewMessage($messageBody, $messageBody, $sentFromUser, true, false);
			}
		}

		$this->em->flush();

		return $messages;
	}

	protected function addNewEnvelope($message, $folder, $ownedByUser, $sentToUser, $isDraft, $isRead, $isFlagged, $persist = true, $andFlush = true)
	{
		$envelope = new Envelope();
		
		$message->setSendTo($message->getSendTo() . ', ' . $sentToUser->getUsername());
		
		$envelope->setMessage($message);
		$envelope->setFolder($folder);
		$envelope->setThread($message->getThread());
		$envelope->setOwnedByUser($ownedByUser);
		$envelope->setSentToUser($sentToUser);
		$envelope->setSentDate($message->getCreatedDate());
		$envelope->setDraft($isDraft);
		$envelope->setRead($isRead);
		$envelope->setFlagged($isFlagged);
		
		if ($persist) {
			$this->em->persist($envelope);

			if ($andFlush) {
				$this->em->flush();
				$this->em->refresh($envelope);
			}
		}
		
		return $envelope;
	}

	protected function addFixturesForEnvelopes($messages, $folders, $users)
	{
		$envelopes = array();

		foreach ($users as $sentToUser) {
			foreach ($folders as $folder) {
				foreach ($messages as $message) {
					$envelopes[] = $this->addNewEnvelope($message, $folder, $sentToUser, $sentToUser, false, false, false, true, false);
				}
			}
		}
		
		$this->em->flush();
		
		return $envelopes;
	}

	protected function addNewRegistry($user, $persist = true, $andFlush = true)
	{
		$registry = new Registry();
		
		$registry->setOwnedByUser($user);
		$registry->setCachedUnreadMessageCount(0);

		if ($persist) {
			$this->em->persist($registry);
		
			if ($andFlush) {
				$this->em->flush();
				$this->em->refresh($registry);
			}
		}
		
		return $registry;
	}

	protected function addFixturesForRegistries($users)
	{
		$registries = array();
		
		foreach ($users as $user) {
			$registries[] = $this->addNewRegistry($user, true, false);
		}
		
		$this->em->flush();
		
		return $registries;
	}

    /**
     *
     * @var \CCDNMessage\MessageBundle\Model\Model\FolderModel $folderModel
     */
    private $folderModel;

    /**
     *
     * @var \CCDNMessage\MessageBundle\Model\Model\MessageModel $messageModel
     */
    private $messageModel;

    /**
     *
     * @var \CCDNMessage\MessageBundle\Model\Model\EnvelopeModel $envelopeModel
     */
    private $envelopeModel;

    /**
     *
     * @var \CCDNMessage\MessageBundle\Model\Model\ThreadModel $threadModel
     */
    private $threadModel;

    /**
     *
     * @var \CCDNMessage\MessageBundle\Model\Model\RegistryModel $registryModel
     */
    private $registryModel;

    /**
     *
     * @var \CCDNMessage\MessageBundle\Model\Model\UserModel $userModel
     */
    private $userModel;

    /**
     *
     * @access protected
     * @return \CCDNMessage\MessageBundle\Model\Model\FolderModel
     */
    protected function getFolderModel()
    {
        if (null == $this->folderModel) {
            $this->folderModel = $this->container->get('ccdn_message_message.model.folder');
        }

        return $this->folderModel;
    }

    /**
     *
     * @access protected
     * @return \CCDNMessage\MessageBundle\Model\Model\MessageModel
     */
    protected function getMessageModel()
    {
        if (null == $this->messageModel) {
            $this->messageModel = $this->container->get('ccdn_message_message.model.message');
        }

        return $this->messageModel;
    }

    /**
     *
     * @access protected
     * @return \CCDNMessage\MessageBundle\Model\Model\EnvelopeModel
     */
    protected function getEnvelopeModel()
    {
        if (null == $this->envelopeModel) {
            $this->envelopeModel = $this->container->get('ccdn_message_message.model.envelope');
        }

        return $this->envelopeModel;
    }

    /**
     *
     * @access protected
     * @return \CCDNMessage\MessageBundle\Model\Model\ThreadModel
     */
    protected function getThreadModel()
    {
        if (null == $this->threadModel) {
            $this->threadModel = $this->container->get('ccdn_message_message.model.thread');
        }

        return $this->threadModel;
    }

    /**
     *
     * @access protected
     * @return \CCDNMessage\MessageBundle\Model\Model\RegistryModel
     */
    protected function getRegistryModel()
    {
        if (null == $this->registryModel) {
            $this->registryModel = $this->container->get('ccdn_message_message.model.registry');
        }

        return $this->registryModel;
    }

    /**
     *
     * @access protected
     * @return \CCDNMessage\MessageBundle\Model\Model\UserModel
     */
    protected function getUserModel()
    {
        if (null == $this->userModel) {
            $this->userModel = $this->container->get('ccdn_message_message.model.user');
        }

        return $this->userModel;
    }
}
