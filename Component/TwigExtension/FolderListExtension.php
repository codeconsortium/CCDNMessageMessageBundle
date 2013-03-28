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

namespace CCDNMessage\MessageBundle\Component\TwigExtension;

use CCDNMessage\MessageBundle\Manager\BaseManagerInterface;
use Symfony\Component\Security\Core\SecurityContext;

/**
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
class FolderListExtension extends \Twig_Extension
{
    /**
     *
     * @access protected
	 * @var \CCDNMessage\MessageBundle\Manager\BaseManagerInterface $folderManager
     */
    protected $folderManager;

	/**
	 * 
	 * @access protected
	 * @var \Symfony\Component\Security\Core\SecurityContext $securityContext
	 */
	protected $securityContext;
	
    /**
     * 
	 * @access public
	 * @param \CCDNMessage\MessageBundle\Manager\BaseManagerInterface $folderManager
	 * @param \Symfony\Component\Security\Core\SecurityContext $securityContext
     */
    public function __construct(BaseManagerInterface $folderManager, SecurityContext $securityContext)
    {
        $this->folderManager = $folderManager;
		$this->securityContext = $securityContext;
    }

    /**
     *
     * @access public
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'message_bundle_folder_list' => new \Twig_Function_Method($this, 'folderList'),
        );
    }

    /**
     * Gets all boards available with their categories.
     *
     * @access public
     * @return array
     */
    public function folderList()
    {
		$userId = $this->securityContext->getToken()->getUser()->getId();
		
		if (null == $userId) {
			return array(
				'quota' => array(
					'allowed' => '0',
					'used'    => '0',
				),
				'folders' => array(),
			);
		}
		
		return array(
			'quota' => array(
				'allowed' => '0',
				'used'    => '0',
			),
			'folders' => $this->folderManager->findAllFoldersForUserById($userId),
		);
    }

    /**
     *
     * @access public
     * @return string
     */
    public function getName()
    {
        return 'folderList';
    }
}