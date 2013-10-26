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

namespace CCDNMessage\MessageBundle\Entity;

use CCDNMessage\MessageBundle\Entity\Model\RegistryModel;

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
class Registry extends RegistryModel
{
    /**
     *
     * @var integer $id
     */
    protected $id;

    /**
     *
     * @var integer $cachedUnreadMessageCount
     */
    protected $cachedUnreadMessageCount = 0;

    /**
     *
     * @access public
     */
    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get cachedUnreadMessageCount
     *
     * @return integer
     */
    public function getCachedUnreadMessageCount()
    {
        return $this->cachedUnreadMessageCount;
    }

    /**
     * Set cachedUnreadMessageCount
     *
     * @param  integer  $cachedUnreadMessageCount
     * @return Registry
     */
    public function setCachedUnreadMessageCount($cachedUnreadMessageCount)
    {
        $this->cachedUnreadMessageCount = $cachedUnreadMessageCount;

        return $this;
    }
}
