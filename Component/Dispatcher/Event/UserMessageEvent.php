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

namespace CCDNMessage\MessageBundle\Component\Dispatcher\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

use CCDNMessage\MessageBundle\Entity\Message;

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
class UserMessageEvent extends Event
{
    /**
     *
     * @access protected
     * @var \Symfony\Component\HttpFoundation\Request $request
     */
    protected $request;

    /**
     *
     * @access protected
     * @var \CCDNMessage\MessageBundle\Entity\Message $message
     */
    protected $message;

    /**
     *
     * @access protected
     * @var bool $subscribe
     */
    protected $subscribe;

    /**
     *
     * @access public
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \CCDNMessage\MessageBundle\Entity\Message     $message
     * @param bool                                      $subscribe
     */
    public function __construct(Request $request, Message $message = null, $subscribe = false)
    {
        $this->request = $request;
        $this->message = $message;
        $this->subscribe = $subscribe;
    }

    /**
     *
     * @access public
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     *
     * @access public
     * @return \CCDNMessage\MessageBundle\Entity\Message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     *
     * @access public
     * @return bool
     */
    public function authorWantsToSubscribe()
    {
        return $this->subscribe;
    }
}
