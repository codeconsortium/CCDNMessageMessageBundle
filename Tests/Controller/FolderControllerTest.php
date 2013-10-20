<?php

namespace CCDNMessage\MessageBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FolderControllerTest extends WebTestCase
{
		
	
	
    /**
	 *
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
	
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
    }
	

	public function testShowFolderByNameAction()
	{
		//$client = static::createClient();
		
		// ccdn_message_message_user_index:
	    // 	pattern: /{_locale}/message
	    // 	defaults: { _controller: CCDNMessageMessageBundle:Folder:showFolderByName, _locale: en, folderName: inbox, page: 0 }
        // 
		// #
		// # Folders.
		// #
		// ccdn_message_message_user_folder_show:
		//     pattern: /{_locale}/message/folder/{folderName}
		//     defaults: { _controller: CCDNMessageMessageBundle:Folder:showFolderByName, _locale: en, folderName: inbox, page: 0 }
		//     requirements:
		//         folder_name: inbox|sent|drafts|junk|trash
        // 
		// ccdn_message_message_user_folder_show_paginated:
		//     pattern: /{_locale}/message/folder/{folderName}/page/{page}
		//     defaults: { _controller: CCDNMessageMessageBundle:Folder:showFolderByName, _locale: en, folderName: inbox, page: 0 }
		//     requirements:
		//         folder_name: inbox|sent|drafts|junk|trash
        // 
		// ccdn_message_message_user_folder_show_by_id:
		//     pattern: /{_locale}/message/folder/{folderId}
		//     defaults: { _controller: CCDNMessageMessageBundle:Folder:showFolderById, _locale: en, page: 0 }
        // 
		// ccdn_message_message_user_folder_show_by_id_paginated:
		//     pattern: /{_locale}/message/folder/{folderId}/page/{page}
		//     defaults: { _controller: CCDNMessageMessageBundle:Folder:showFolderById, _locale: en, page: 0 }
		
		$client = static::createClient(array(), array(
    		'PHP_AUTH_USER' => 'admin',
    		'PHP_AUTH_PW'   => 'root',
		));
		
		$client->followRedirects();
		
		$crawler = $client->request('GET', '/en/message');
		
//		        print_r($client->getResponse());
//        die();
		
//		$this->assertTrue($this->container->get('security.context')->isGranted('ROLE_USER'));	
		
		//$this->assertGreaterThan(0, $crawler->filter('html:contains("dashboard")')->count());
//		$this->assertGreaterThan(0, $crawler->filter('html:contains("Subject")')->count());

	}
}