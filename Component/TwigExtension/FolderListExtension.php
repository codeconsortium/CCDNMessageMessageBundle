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

use CCDNMessage\MessageBundle\Model\Model\ModelInterface;
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
     * @var \CCDNMessage\MessageBundle\Model\Model\ModelInterface $folderModel
     */
    protected $folderModel;

    /**
     *
     * @access protected
     * @var \Symfony\Component\Security\Core\SecurityContext $securityContext
     */
    protected $securityContext;

    /**
     *
     * @access public
     * @param \CCDNMessage\MessageBundle\Model\Model\ModelInterface $folderManager
     * @param \Symfony\Component\Security\Core\SecurityContext      $securityContext
     */
    public function __construct(ModelInterface $folderModel, SecurityContext $securityContext)
    {
        $this->folderModel = $folderModel;
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
     * Gets all folders available with their categories.
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
            'folders' => $this->folderModel->findAllFoldersForUserById($userId),
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
