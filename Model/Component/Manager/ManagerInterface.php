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

namespace CCDNMessage\MessageBundle\Model\Component\Manager;

use Symfony\Component\EventDispatcher\EventDispatcherInterface ;
use Doctrine\ORM\QueryBuilder;
use CCDNMessage\MessageBundle\Model\Component\Gateway\GatewayInterface;
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
 */
interface ManagerInterface
{
    /**
     *
     * @access public
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface         $dispatcher
     * @param \CCDNMessage\MessageBundle\Model\Component\Gateway\GatewayInterface $gateway
     */
    public function __construct(EventDispatcherInterface  $dispatcher, GatewayInterface $gateway);

    /**
     *
     * @access public
     * @param  \CCDNMessage\MessageBundle\Model\FrontModel\ModelInterface                $model
     * @return \CCDNMessage\MessageBundle\Model\Component\Repository\RepositoryInterface
     */
    public function setModel(ModelInterface $model);

    /**
     *
     * @access public
     * @return \CCDNMessage\MessageBundle\Model\Component\Gateway\GatewayInterface
     */
    public function getGateway();

    /**
     *
     * @access public
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilder();

    /**
     *
     * @access public
     * @param  string                                       $column  = null
     * @param  Array                                        $aliases = null
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function createCountQuery($column = null, Array $aliases = null);

    /**
     *
     * @access public
     * @param  Array                                        $aliases = null
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function createSelectQuery(Array $aliases = null);

    /**
     *
     * @access public
     * @param  \Doctrine\ORM\QueryBuilder                   $qb
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function one(QueryBuilder $qb);

    /**
     *
     * @access public
     * @param  \Doctrine\ORM\QueryBuilder $qb
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function all(QueryBuilder $qb);

    /**
     *
     * @access public
     * @param  Object                                                              $entity
     * @return \CCDNMessage\MessageBundle\Model\Component\Manager\ManagerInterface
     */
    public function persist($entity);

    /**
     *
     * @access public
     * @param  Object                                                              $entity
     * @return \CCDNMessage\MessageBundle\Model\Component\Manager\ManagerInterface
     */
    public function remove($entity);

    /**
     *
     * @access public
     * @return \CCDNMessage\MessageBundle\Model\Component\Manager\ManagerInterface
     */
    public function flush();

    /**
     *
     * @access public
     * @param  Object                                                              $entity
     * @return \CCDNMessage\MessageBundle\Model\Component\Manager\ManagerInterface
     */
    public function refresh($entity);
}
