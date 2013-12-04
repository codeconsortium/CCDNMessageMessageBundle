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
use CCDNMessage\MessageBundle\Component\Helper\RegistryHelper;

/**
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
class UnreadMessageCountExtension extends \Twig_Extension
{
    /**
     *
     * @access protected
     * @var \CCDNMessage\MessageBundle\Component\Helper\RegistryHelper $registryHelper
     */
    protected $registryHelper;

    /**
     *
     * @access protected
     * @var \Symfony\Component\Security\Core\SecurityContext $securityContext
     */
    protected $securityContext;

    /**
     *
     * @access public
     * @param \CCDNMessage\MessageBundle\Component\Helper\RegistryHelper $registryHelper
     * @param \Symfony\Component\Security\Core\SecurityContext           $securityContext
     */
    public function __construct(RegistryHelper $registryHelper, SecurityContext $securityContext)
    {
        $this->registryHelper = $registryHelper;
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
            'unreadMessageCount' => new \Twig_Function_Method($this, 'unreadMessageCount'),
        );
    }

    /**
     * Gets the number of unread messages that is cached in the message registry.
     *
     * @access public
     * @return int
     */
    public function unreadMessageCount()
    {
        $user = $this->securityContext->getToken()->getUser();

        $unreadMessageCount = $this->registryHelper->findOneRegistryForUserByIdId($user);

        if ($unreadMessageCount == null) {
            return 0;
        }

        return $unreadMessageCount->getCachedUnreadMessageCount();
    }

    /**
     *
     * @access public
     * @return string
     */
    public function getName()
    {
        return 'unreadMessageCount';
    }
}
