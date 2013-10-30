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

namespace CCDNMessage\MessageBundle\features\bootstrap;

use Behat\Behat\Context\BehatContext;
use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use Symfony\Component\HttpKernel\KernelInterface;

use CCDNMessage\MessageBundle\Tests\Functional\src\Entity\User;

use CCDNMessage\MessageBundle\Entity\Folder;
use CCDNMessage\MessageBundle\Entity\Message;
use CCDNMessage\MessageBundle\Entity\Envelope;
use CCDNMessage\MessageBundle\Entity\Thread;
use CCDNMessage\MessageBundle\Entity\Registry;

/**
 *
 * Features context.
 *
 * @category CCDNMessage
 * @package  MessageBundle
 *
 * @author   Reece Fowell <reece@codeconsortium.com>
 * @license  http://opensource.org/licenses/MIT MIT
 * @version  Release: 2.0
 * @link     https://github.com/codeconsortium/CCDNMessageMessageBundle
 *
 */
class DataContext extends BehatContext implements KernelAwareInterface
{
    /**
     *
     * Kernel.
     *
     * @var KernelInterface
     */
    protected $kernel;

    public function __construct()
    {

    }

    /**
     *
     * {@inheritdoc}
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     *
     * Get entity manager.
     *
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->getContainer()->get('doctrine')->getManager();
    }

    /**
     *
     * Returns Container instance.
     *
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        return $this->kernel->getContainer();
    }

    /**
     *
     * Get service by id.
     *
     * @param string $id
     *
     * @return object
     */
    protected function getService($id)
    {
        return $this->getContainer()->get($id);
    }

    protected $users = array();

    /**
     *
     * @Given /^there are following users defined:$/
     */
    public function thereAreFollowingUsersDefined(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
			$username = isset($data['name']) ? $data['name'] : sha1(uniqid(mt_rand(), true));
			
            $this->users[$username] = $this->thereIsUser(
                $username,
                isset($data['email']) ? $data['email'] : sha1(uniqid(mt_rand(), true)),
                isset($data['password']) ? $data['password'] : 'password',
                isset($data['role']) ? $data['role'] : 'ROLE_USER',
                isset($data['enabled']) ? $data['enabled'] : true,
				true,
				false
            );
        }
		
		$this->addFixturesForFolders($this->users);
		
		$this->getEntityManager()->flush();
    }

    public function thereIsUser($username, $email, $password, $role = 'ROLE_USER', $enabled = true, $persist = true, $andFlush = true)
    {
        $user = new User();

        $user->setUsername($username);
        $user->setEmail($email);
        $user->setEnabled($enabled);
        $user->setPlainPassword($password);

        if (null !== $role) {
            $user->addRole($role);
        }
		
		if ($persist) {
	        $this->getEntityManager()->persist($user);
			
			if ($andFlush) {
		        $this->getEntityManager()->flush();
			}
		}

        return $user;
    }

	protected function addNewFolder($folderName, $ownedByUser, $specialType, $persist = true, $andFlush = true)
	{
		$folder = new Folder();
		
		$folder->setName($folderName);
		$folder->setOwnedByUser($ownedByUser);
		$folder->setSpecialType($specialType);
		
		if ($persist) {
			$this->getEntityManager()->persist($folder);
		
			if ($andFlush) {
				$this->getEntityManager()->flush();
				//$this->getEntityManager()->refresh($folder);
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
		
		$this->getEntityManager()->flush();
		
		return $folders;
	}

    protected $messages = array();

    /**
     *
     * @Given /^there are following messages defined:$/
     */
    public function thereAreFollowingMessagesDefined(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $this->messages[] = $this->thereIsMessage(
                isset($data['subject']) ? $data['subject'] : sha1(uniqid(mt_rand(), true)),
                isset($data['body']) ? $data['body'] : sha1(uniqid(mt_rand(), true)),
                isset($data['from']) ? $data['from'] : $index,
                isset($data['to']) ? $data['to'] : $index,
				isset($data['folder']) ? $data['folder'] : 'inbox',
				true,
				false
            );
        }
		
		$this->getEntityManager()->flush();
    }

    public function thereIsMessage($messageSubject, $messageBody, $sentFromUserName, $sentToUserName, $folderName, $persist = true, $andFlush = true)
    {
		$message = new Message();
		
		$message->setSentFromUser($sentFromUser);
		$message->setSubject($messageSubject);
		$message->setBody($messageBody);
		$message->setCreatedDate(new \Datetime('now'));
		$message->setSendTo(' ');
		$message->setThread(new Thread());
		
		if ($persist) {
			$this->getEntityManager()->persist($message);
		
			if ($andFlush) {
				$this->getEntityManager()->flush();
				//$this->getEntityManager()->refresh($message);
			}
		}
		
		foreach ($this->users as $user) {
			if ($user->getUsername() == $sentToUserName) {
				$sentToUser = $user;

				$recipientUsersFolders = array();
				foreach ($folders as $folder) {
					if ($folder->getOwnedByUser()->getId() == $sentToUser->getId()) {
						$recipientUsersFolders[$folder->getName()] = $folder;
					}
				}
			}
			
			if ($user->getUsername() == $sentFromUserName) {
				$sentFromUser = $user;
				
				$sendersUsersFolders = array();
				foreach ($folders as $folder) {
					if ($folder->getOwnedByUser()->getId() == $sentFromUser->getId()) {
						$sendersUsersFolders[$folder->getName()] = $folder;
					}
				}
			}
		}
		
		$envelopes = array();
		$envelopes[] = $this->addNewEnvelope($message, $recipientUsersFolders['inbox'], $sentToUser, $sentToUser, false, false, false, true, false);
		$envelopes[] = $this->addNewEnvelope($message, $sendersUsersFolders['sent'], $sentFromUser, $sentToUser, false, false, false, true, false);
		
		$this->getEntityManager()->flush();
		
		return $message;
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
			$this->getEntityManager()->persist($envelope);

			if ($andFlush) {
				$this->getEntityManager()->flush();
				//$this->getEntityManager()->refresh($envelope);
			}
		}
		
		return $envelope;
	}
}
