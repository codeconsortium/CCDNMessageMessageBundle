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

use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
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
 * @abstract
 *
 */
abstract class BaseManager
{
    /**
     *
     * @access protected
     * @var \CCDNMessage\MessageBundle\Model\Component\Manager\ManagerInterface $gateway
     */
    protected $gateway;

    /**
     *
     * @access protected
     * @var \CCDNMessage\MessageBundle\Model\FrontModel\ModelInterface $model
     */
    protected $model;

    /**
     *
     * @access protected
     * @var \Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher  $dispatcher
     */
    protected $dispatcher;

    /**
     *
     * @access public
     * @param \Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher    $dispatcher
     * @param \CCDNMessage\MessageBundle\Model\Component\Gateway\GatewayInterface $gateway
     */
    public function __construct(ContainerAwareEventDispatcher  $dispatcher, GatewayInterface $gateway)
    {
        $this->dispatcher = $dispatcher;
        $this->gateway = $gateway;
    }

    /**
     *
     * @access public
     * @param  \CCDNMessage\MessageBundle\Model\FrontModel\ModelInterface                $model
     * @return \CCDNMessage\MessageBundle\Model\Component\Repository\RepositoryInterface
     */
    public function setModel(ModelInterface $model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     *
     * @access public
     * @return \CCDNMessage\MessageBundle\Model\Component\Gateway\GatewayInterface
     */
    public function getGateway()
    {
        return $this->gateway;
    }

    /**
     *
     * @access public
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->gateway->getQueryBuilder();
    }

    /**
     *
     * @access public
     * @param  string                                       $column  = null
     * @param  Array                                        $aliases = null
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function createCountQuery($column = null, Array $aliases = null)
    {
        return $this->gateway->createCountQuery($column, $aliases);
    }

    /**
     *
     * @access public
     * @param  Array                                        $aliases = null
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function createSelectQuery(Array $aliases = null)
    {
        return $this->gateway->createSelectQuery($aliases);
    }

    /**
     *
     * @access public
     * @param  \Doctrine\ORM\QueryBuilder                   $qb
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function one(QueryBuilder $qb)
    {
        return $this->gateway->one($qb);
    }

    /**
     *
     * @access public
     * @param  \Doctrine\ORM\QueryBuilder $qb
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function all(QueryBuilder $qb)
    {
        return $this->gateway->all($qb);
    }

    /**
     *
     * @access public
     * @param  Object                                                              $entity
     * @return \CCDNMessage\MessageBundle\Model\Component\Manager\ManagerInterface
     */
    public function persist($entity)
    {
        $this->gateway->persist($entity);

        return $this;
    }

    /**
     *
     * @access public
     * @param  Object                                                              $entity
     * @return \CCDNMessage\MessageBundle\Model\Component\Manager\ManagerInterface
     */
    public function remove($entity)
    {
        $this->gateway->remove($entity);

        return $this;
    }

    /**
     *
     * @access public
     * @return \CCDNMessage\MessageBundle\Model\Component\Manager\ManagerInterface
     */
    public function flush()
    {
        $this->gateway->flush();

        return $this;
    }

    /**
     *
     * @access public
     * @param  Object                                                              $entity
     * @return \CCDNMessage\MessageBundle\Model\Component\Manager\ManagerInterface
     */
    public function refresh($entity)
    {
        $this->gateway->refresh($entity);

        return $this;
    }
}
