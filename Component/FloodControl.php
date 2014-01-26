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

namespace CCDNMessage\MessageBundle\Component;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\HttpFoundation\Session\Session;

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
class FloodControl
{
    /**
     *
     * @access protected
     * @var \Symfony\Component\Security\Core\SecurityContextInterface $securityContext
     */
    protected $securityContext;

    /**
     *
     * @access protected
     * @var \Symfony\Component\HttpFoundation\Session\Session $session
     */
    protected $session;

    /**
     *
     * @access protected
     * @var string $kernelEnv
     */
    protected $kernelEnv;

    /**
     *
     * @access protected
     * @var int $sendLimit
     */
    protected $sendLimit;

    /**
     *
     * @access protected
     * @var int $blockForMinutes
     */
    protected $blockForMinutes;

    /**
     *
     * @access public
     * @param  \Symfony\Component\Security\Core\SecurityContextInterface $securityContext
     * @param  \Symfony\Component\HttpFoundation\Session\Session         $session
     * @param  string                                                    $kernelEnv
     * @param  int                                                       $sendLimit
     * @param  int                                                       $blockForMinutes
     */
    public function __construct(SecurityContextInterface $securityContext, Session $session, $kernelEnv, $sendLimit, $blockForMinutes)
    {
        $this->securityContext = $securityContext;
        $this->session = $session;
        $this->kernelEnv = $kernelEnv;

        if ( ! $this->session->has('flood_control_message_send_count')) {
            $this->session->set('flood_control_message_send_count', array());
        }

        $this->sendLimit = $sendLimit;
        $this->blockForMinutes = $blockForMinutes;
    }

    /**
     *
     * @access public
     */
    public function incrementCounter()
    {
        if (! $this->securityContext->isGranted('ROLE_MODERATOR') || $this->kernelEnv != 'prod') {
	        $sendCount = $this->session->get('flood_control_message_send_count');

	        $sendCount[] = new \DateTime('now');

	        $this->session->set('flood_control_message_send_count', $sendCount);
		}
    }

    /**
     *
     * @access public
     * @return bool
     */
    public function isFlooded()
    {
        if ($this->sendLimit < 1 || ! $this->securityContext->isGranted('ROLE_MODERATOR') || $this->kernelEnv != 'prod') {
			return false;
		}

        if ($this->session->has('flood_control_message_send_count')) {
            $attempts = $this->session->get('flood_control_message_send_count');

            // Iterate over attempts and only reveal attempts that fall within the $timeLimit.
            $freshenedAttempts = array();

	        $timeLimit = new \DateTime('-' . $this->blockForMinutes . ' minutes');
            $limit = $timeLimit->getTimestamp();

            foreach ($attempts as $attempt) {
                $date = $attempt->getTimestamp();

                if ($date > $limit) {
                    $freshenedAttempts[] = $attempt;
                }
            }

            if (count($freshenedAttempts) > $this->sendLimit) {
                return true;
            }
        }

        return false;
    }

}
