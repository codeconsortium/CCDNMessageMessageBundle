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

namespace CCDNMessage\MessageBundle\Model\Repository;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\QueryBuilder;
use CCDNMessage\MessageBundle\Model\Gateway\GatewayInterface;
use CCDNMessage\MessageBundle\Model\Model\ModelInterface;

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
abstract class BaseRepository
{
    /**
     *
     * @access protected
     * @var \Doctrine\ORM\EntityManager $em
     */
    protected $em;

    /**
     *
     * @access protected
     * @var \CCDNMessage\MessageBundle\Model\Gateway\GatewayInterface $gateway
     */
    protected $gateway;

    /**
     *
     * @access protected
     * @var \CCDNMessage\MessageBundle\Model\Model\ModelInterface $model
     */
    protected $model;

    /**
     *
     * @access public
     * @param \Doctrine\Common\Persistence\ObjectManager                $em
     * @param \CCDNMessage\MessageBundle\Model\Gateway\GatewayInterface $gateway
     */
    public function __construct(ObjectManager $em, GatewayInterface $gateway)
    {
        $this->em = $em;
        $this->gateway = $gateway;
    }

    /**
     *
     * @access public
     * @param  \CCDNMessage\MessageBundle\Model\Model\ModelInterface           $model
     * @return \CCDNMessage\MessageBundle\Model\Repository\RepositoryInterface
     */
    public function setModel(ModelInterface $model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     *
     * @access public
     * @return \CCDNMessage\MessageBundle\Model\Gateway\GatewayInterface
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
}
