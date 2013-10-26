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
class QuotaHelper
{
	protected $quota;
	
	public function __construct($quota)
	{
		$this->quota = $quota;
	}
	
    /**
     *
     * @access public
     * @param  array $folders
     * @return int
     */
    public function isQuotaAllowanceFilled($folders)
    {
		$used = $this->getUsedAllowance($folders, $this->quota);
		
		if ($used['used_allowance'] < 101) {
			return false;
		} else {
			return true;
		}
    }

    /**
     *
     * @access public
     * @param  array $folders
     * @param  int   $quota
     * @return array
     */
    public function getUsedAllowance($folders, $quota)
    {
        $totalMessageCount = 0;

        foreach ($folders as $key => $folder) {
            $totalMessageCount += $folder->getCachedTotalMessageCount();
        }

        $usedAllowance = ($totalMessageCount / $quota) * 100;

        // where 100 represents 100%, if the number should fractionally exceed 10 then reset it to 100%
        if ($usedAllowance > 100) {
            $usedAllowance = 100;
        }

        return array(
            'used_allowance' => $usedAllowance,
            'total_message_count' => $totalMessageCount
        );
    }
}