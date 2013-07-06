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

namespace CCDNMessage\MessageBundle\Gateway;

use Doctrine\ORM\QueryBuilder;

use CCDNMessage\MessageBundle\Gateway\BaseGatewayInterface;
use CCDNMessage\MessageBundle\Gateway\BaseGateway;

use CCDNMessage\MessageBundle\Entity\Message;

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
class MessageGateway extends BaseGateway implements BaseGatewayInterface
{
    /**
     *
     * @access private
     * @var string $queryAlias
     */
    protected $queryAlias = 'm';

    /**
     *
     * @access public
     * @param  \Doctrine\ORM\QueryBuilder                   $qb
     * @param  Array                                        $parameters
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findMessage(QueryBuilder $qb = null, $parameters = null)
    {
        if (null == $qb) {
            $qb = $this->createSelectQuery();
        }

        return $this->one($qb, $parameters);
    }

    /**
     *
     * @access public
     * @param  \Doctrine\ORM\QueryBuilder                   $qb
     * @param  Array                                        $parameters
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findMessages(QueryBuilder $qb = null, $parameters = null)
    {
        if (null == $qb) {
            $qb = $this->createSelectQuery();
        }

        $qb
            ->addOrderBy('m.createdDate', 'DESC')
            ->addOrderBy('m.sentDate', 'DESC')
        ;

        return $this->all($qb, $parameters);
    }

    /**
     *
     * @access public
     * @param  \Doctrine\ORM\QueryBuilder $qb
     * @param  Array                      $parameters
     * @return int
     */
    public function countMessages(QueryBuilder $qb = null, $parameters = null)
    {
        if (null == $qb) {
            $qb = $this->createCountQuery();
        }

        if (null == $parameters) {
            $parameters = array();
        }

        $qb->setParameters($parameters);

        try {
            return $qb->getQuery()->getSingleScalarResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return 0;
        }
    }

    /**
     *
     * @access public
     * @param  \CCDNMessage\MessageBundle\Entity\Message               $message
     * @return \CCDNMessage\MessageBundle\Gateway\BaseGatewayInterface
     */
    public function persistMessage(Message $message)
    {
        $this->persist($message)->flush();

        return $this;
    }

    /**
     *
     * @access public
     * @param  \CCDNMessage\MessageBundle\Entity\Message               $message
     * @return \CCDNMessage\MessageBundle\Gateway\BaseGatewayInterface
     */
    public function updateMessage(Message $message)
    {
        $this->persist($message)->flush();

        return $this;
    }

    /**
     *
     * @access public
     * @param  \CCDNMessage\MessageBundle\Entity\Message               $message
     * @return \CCDNMessage\MessageBundle\Gateway\BaseGatewayInterface
     */
    public function deleteMessage(Message $message)
    {
        $this->remove($message)->flush();

        return $this;
    }
}
