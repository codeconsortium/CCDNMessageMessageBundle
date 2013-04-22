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

namespace CCDNMessage\MessageBundle\Form\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

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
 * @see http://symfony.com/doc/current/cookbook/validation/custom_constraint.html
 *
 */
class SendTo extends Constraint
{
    /**
     *
     * @access public
     */
    public $message = 'The user "%username%" were not found.';

    /**
     *
     * @access public
     * @return string
     */
    public function validatedBy()
    {
        return 'SendToValidator';
    }
}
