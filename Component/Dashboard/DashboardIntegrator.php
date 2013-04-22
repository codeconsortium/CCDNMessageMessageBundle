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

namespace CCDNMessage\MessageBundle\Component\Dashboard;

use CCDNComponent\DashboardBundle\Component\Integrator\Model\BuilderInterface;

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
class DashboardIntegrator
{
    /**
     *
     * @access public
     * @param CCDNComponent\DashboardBundle\Component\Integrator\Model\BuilderInterface $builder
     */
    public function build(BuilderInterface $builder)
    {
        $builder
            ->addCategory('account')
                ->setLabel('ccdn_message_message.dashboard.categories.account', array(), 'CCDNMessageMessageBundle')
                ->addPages()
                    ->addPage('account')
                        ->setLabel('ccdn_message_message.dashboard.pages.account', array(), 'CCDNMessageMessageBundle')
                    ->end()
                ->end()
                ->addLinks()
                    ->addLink('messages')
                        ->setAuthRole('ROLE_USER')
                        ->setRoute('ccdn_message_message_index')
                        ->setIcon('/bundles/ccdncomponentcommon/images/icons/Black/32x32/32x32_email.png')
                        ->setLabel('ccdn_message_message.title.folder.index', array(), 'CCDNMessageMessageBundle')
                    ->end()
                ->end()
            ->end()
        ;
    }
}
