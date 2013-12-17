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

namespace CCDNMessage\MessageBundle\Model\Component\Manager;

use CCDNMessage\MessageBundle\Model\Component\Manager\ManagerInterface;
use CCDNMessage\MessageBundle\Model\Component\Manager\BaseManager;

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
class ThreadManager extends BaseManager implements ManagerInterface
{
    /**
     *
     * @access public
     * @return \CCDNMessage\MessageBundle\Entity\Thread
     */
    public function createThread()
    {
        return $this->gateway->createThread();
    }

}
