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

namespace CCDNMessage\MessageBundle\Component\Helper;

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
class PaginationConfigHelper
{
    /**
     *
     * @var int $messagesPerPageOnFolders
     */
    protected $messagesPerPageOnFolders;

    /**
     *
     * @access public
     * @param int $messagesPerPageOnFolders
     */
    public function __construct($messagesPerPageOnFolders)
    {
        $this->messagesPerPageOnFolders = $messagesPerPageOnFolders;
    }

    /**
     *
     * @access public
     * @return int
     */
    public function getMessagesPerPageOnFolders()
    {
        return $this->messagesPerPageOnFolders;
    }
}
