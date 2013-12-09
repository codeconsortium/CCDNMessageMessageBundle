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

namespace CCDNMessage\MessageBundle\Model\Component\Gateway;

use Doctrine\ORM\QueryBuilder;

use CCDNMessage\MessageBundle\Model\Component\Gateway\GatewayInterface;
use CCDNMessage\MessageBundle\Model\Component\Gateway\BaseGateway;

use CCDNMessage\MessageBundle\Entity\Envelope;

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
class EnvelopeGateway extends BaseGateway implements GatewayInterface
{
    /**
     *
     * @access private
     * @var string $queryAlias
     */
    protected $queryAlias = 'e';

    /**
     *
     * @access public
     * @param  \Doctrine\ORM\QueryBuilder                   $qb
     * @param  Array                                        $parameters
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findEnvelope(QueryBuilder $qb = null, $parameters = null)
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
    public function findEnvelopes(QueryBuilder $qb = null, $parameters = null)
    {
        if (null == $qb) {
            $qb = $this->createSelectQuery();
        }

        $qb
            ->addOrderBy('e.sentDate', 'DESC')
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
    public function countEnvelopes(QueryBuilder $qb = null, $parameters = null)
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
     * @param  \CCDNMessage\MessageBundle\Entity\Envelope                          $envelope
     * @return \CCDNMessage\MessageBundle\Model\Component\Gateway\GatewayInterface
     */
    public function saveEnvelope(Envelope $envelope)
    {
        $this->persist($envelope)->flush();

        return $this;
    }

    /**
     *
     * @access public
     * @param  \CCDNMessage\MessageBundle\Entity\Envelope                          $envelope
     * @return \CCDNMessage\MessageBundle\Model\Component\Gateway\GatewayInterface
     */
    public function updateEnvelope(Envelope $envelope)
    {
        $this->persist($envelope)->flush();

        return $this;
    }

    /**
     *
     * @access public
     * @param  \CCDNMessage\MessageBundle\Entity\Envelope                          $envelope
     * @return \CCDNMessage\MessageBundle\Model\Component\Gateway\GatewayInterface
     */
    public function deleteEnvelope(Envelope $envelope)
    {
        $this->remove($envelope)->flush();

        return $this;
    }

	/**
	 * 
	 * @access public
	 * @return \CCDNMessage\MessageBundle\Entity\Envelope
	 */
	public function createEnvelope()
	{
		return new $this->entityClass();
	}
}
