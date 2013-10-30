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

namespace CCDNMessage\MessageBundle\Model\Model;

use Symfony\Component\Security\Core\User\UserInterface;

use CCDNMessage\MessageBundle\Model\Model\BaseModel;
use CCDNMessage\MessageBundle\Model\Model\ModelInterface;

use CCDNMessage\MessageBundle\Entity\Registry;

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
class RegistryModel extends BaseModel implements ModelInterface
{
    /**
     *
     * @access public
     * @param  int                                        $userId
     * @return \CCDNMessage\MessageBundle\Entity\Registry
     */
    public function findOneRegistryForUserById($userId)
    {
		return $this->getRepository()->findOneRegistryForUserById($userId);
    }

    /**
     *
     * @access public
     * @param  \Symfony\Component\Security\Core\User\UserInterface $user
     * @return \CCDNMessage\MessageBundle\Entity\Registry
     */
    public function setupDefaults(UserInterface $user)
    {
        return $this->getManager()->setupDefaults($user);
    }
}