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

use Symfony\Component\Security\Core\SecurityContext;
use CCDNMessage\MessageBundle\Model\FrontModel\RegistryModel;

/**
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
class RegistryExtension extends \Twig_Extension
{
    /**
     *
     * @access protected
     * @var \CCDNMessage\MessageBundle\Model\FrontModel\RegistryModel $registryModel
     */
    protected $registryModel;

    /**
     *
     * @access protected
     * @var \Symfony\Component\Security\Core\SecurityContext $securityContext
     */
    protected $securityContext;

    /**
     *
     * @access public
     * @param \CCDNMessage\MessageBundle\Model\FrontModel\RegistryModel $registryHelper
     * @param \Symfony\Component\Security\Core\SecurityContext          $securityContext
     */
    public function __construct(RegistryModel $registryModel, SecurityContext $securityContext)
    {
        $this->registryModel = $registryModel;
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
            'get_user_message_registry' => new \Twig_Function_Method($this, 'getUserMessageRegistry'),
        );
    }

    /**
     * Gets the number of unread messages that is cached in the message registry.
     *
     * @access public
     * @return int
     */
    public function getUserMessageRegistry()
    {
        if ($this->securityContext->isGranted('ROLE_USER')) {
            $user = $this->securityContext->getToken()->getUser();
            $registry = $this->registryModel->findOrCreateOneRegistryForUser($user);

            return $registry;
        }

        return null;
    }

    /**
     *
     * @access public
     * @return string
     */
    public function getName()
    {
        return 'get_user_message_registry';
    }
}
