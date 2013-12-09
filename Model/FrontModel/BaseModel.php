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

namespace CCDNMessage\MessageBundle\Model\FrontModel;

use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use CCDNMessage\MessageBundle\Model\Component\Manager\ManagerInterface;
use CCDNMessage\MessageBundle\Model\Component\Repository\RepositoryInterface;

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
 * @abstract
 */
abstract class BaseModel
{
    /**
     *
     * @access protected
     * @var \CCDNMessage\MessageBundle\Model\Component\Repository\RepositoryInterface
     */
    protected $repository;

    /**
     *
     * @access protected
     * @var \CCDNMessage\MessageBundle\Model\Component\Manager\ManagerInterface
     */
    protected $manager;

    /**
     *
     * @access protected
     * @var \Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher $dispatcher
     */
    protected $dispatcher;

    /**
     *
     * @access public
	 * @param  \Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher          $dispatcher
     * @param  \CCDNMessage\MessageBundle\Model\Component\Repository\RepositoryInterface $repository
     * @param  \CCDNMessage\MessageBundle\Model\Component\Manager\ManagerInterface       $manager
     */
    public function __construct(ContainerAwareEventDispatcher $dispatcher, RepositoryInterface $repository, ManagerInterface $manager)
    {
		$this->dispatcher = $dispatcher;
		
        $repository->setModel($this);
        $this->repository = $repository;

        $manager->setModel($this);
        $this->manager = $manager;
    }

    /**
     *
     * @access public
     * @return \CCDNMessage\MessageBundle\Model\Component\Repository\RepositoryInterface
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     *
     * @access public
     * @return \CCDNMessage\MessageBundle\Model\Component\Manager\ManagerInterface
     */
    public function getManager()
    {
        return $this->manager;
    }
}
