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

use CCDNMessage\MessageBundle\Entity\Folder;

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
class FolderGateway extends BaseGateway implements GatewayInterface
{
    /**
     *
     * @access private
     * @var string $queryAlias
     */
    protected $queryAlias = 'f';

    /**
     *
     * @access public
     * @param  \Doctrine\ORM\QueryBuilder                   $qb
     * @param  Array                                        $parameters
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findFolder(QueryBuilder $qb = null, $parameters = null)
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
    public function findFolders(QueryBuilder $qb = null, $parameters = null)
    {
        if (null == $qb) {
            $qb = $this->createSelectQuery();
        }

        $qb->addOrderBy('f.specialType', 'ASC');

        return $this->all($qb, $parameters);
    }

    /**
     *
     * @access public
     * @param  \Doctrine\ORM\QueryBuilder $qb
     * @param  Array                      $parameters
     * @return int
     */
    public function countFolders(QueryBuilder $qb = null, $parameters = null)
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
     * @param  \CCDNMessage\MessageBundle\Entity\Folder                            $folder
     * @return \CCDNMessage\MessageBundle\Model\Component\Gateway\GatewayInterface
     */
    public function saveFolder(Folder $folder)
    {
        $this->persist($folder)->flush();

        return $this;
    }

    /**
     *
     * @access public
     * @param  \CCDNMessage\MessageBundle\Entity\Folder                            $folder
     * @return \CCDNMessage\MessageBundle\Model\Component\Gateway\GatewayInterface
     */
    public function updateFolder(Folder $folder)
    {
        $this->persist($folder)->flush();

        return $this;
    }

    /**
     *
     * @access public
     * @param  \CCDNMessage\MessageBundle\Entity\Folder                            $folder
     * @return \CCDNMessage\MessageBundle\Model\Component\Gateway\GatewayInterface
     */
    public function deleteFolder(Folder $folder)
    {
        $this->remove($folder)->flush();

        return $this;
    }

    /**
     *
     * @access public
     * @return \CCDNMessage\MessageBundle\Entity\Folder
     */
    public function createFolder()
    {
        return new $this->entityClass();
    }
}
