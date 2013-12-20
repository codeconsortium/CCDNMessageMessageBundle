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

namespace CCDNMessage\MessageBundle\Form\Validator;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use CCDNMessage\MessageBundle\Model\FrontModel\ModelInterface;

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
class SendToValidator extends ConstraintValidator
{
    /**
     *
     * @access protected
     * @var \CCDNMessage\MessageBundle\Model\FrontModel\ModelInterface $userModel
     */
    protected $userModel;

    /**
     *
     * @access public
     * @param \CCDNMessage\MessageBundle\Model\FrontModel\ModelInterface $userModel
     */
    public function __construct(ModelInterface $userModel)
    {
        $this->userModel = $userModel;
    }

    /**
     *
     * @access public
     * @param  string                                  $value
     * @param  \Symfony\Component\Validator\Constraint $constraint
     * @return bool
     */
    public function validate($value, Constraint $constraint)
    {
        if (strlen($value) < 1) {
            $this->context->addViolation($constraint->message, array('%username%' => $value));

            return;
        }

        $recipients = $this->getRecipients($value);

        if (count($recipients) > 0) {
            $usersFound = $this->userModel->findTheseUsersByUsername($recipients);
        } else {
            $this->context->addViolation($constraint->message, array('%username%' => $value));

            return;
        }

        foreach ($recipients as $recipient) {
            if (! $this->recipientExists($recipient, $usersFound)) {
                $this->context->addViolation($constraint->message, array('%username%' => $recipient));
            }
        }
    }

    /**
     *
     * @access public
     * @param  array $recipient
     * @param  array $usersFound
     * @return bool
     */
    private function recipientExists($recipient, $usersFound)
    {
        foreach ($usersFound as $user) {
            if ($user->getUsername() == $recipient) {
                return true;
            }
        }

        return false;
    }

    /**
     *
     * @access public
     * @param  string $value
     * @return array
     */
    private function getRecipients($value)
    {
        // convert either one user or mulitple users who
        // the mail will be sent to into user entities.
        if ($recipients = preg_split('/((,)|(\s))/', $value, PREG_OFFSET_CAPTURE)) {
            foreach ($recipients as $key => $recipient) {
                // Sanitize the input by removing non-alpha-numeric chars.
                $recipients[$key] = preg_replace("/[^a-zA-Z0-9_]/", "", $recipients[$key]);

                if (! $recipient) {
                    unset($recipients[$key]);
                }
            }
        } else {
            $recipients = array($value);
        }

        return $recipients;
    }
}
