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

namespace CCDNMessage\MessageBundle\Component\Crumbs;

use CCDNMessage\MessageBundle\Component\Crumbs\Factory\CrumbFactory;

/**
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
class BaseCrumbBuilder
{
    /**
     *
     * @access protected
     * @var \CCDNMessage\MessageBundle\Component\Crumbs\Factory\CrumbFactory $crumbFactory
     */
    protected $crumbFactory;

    /**
     *
     * @access public
     * @param \CCDNMessage\MessageBundle\Component\Crumbs\Factory\CrumbFactory $crumbs
     */
    public function __construct(CrumbFactory $crumbFactory)
    {
        $this->crumbFactory = $crumbFactory;
    }

    public function createCrumbTrail()
    {
        return $this->crumbFactory->createNewCrumbTrail();
    }
}
