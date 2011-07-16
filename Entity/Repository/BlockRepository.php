<?php namespace WebDev\ContentBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class BlockRepository
    extends EntityRepository
{
    /**
     * @return WebDev\ContentBundle\Entity\Block[]
     */
    public function findUnbound()
    {
        $query = $this->_em->createQuery("
            SELECT block
            FROM WebDev\ContentBundle\Entity\Block block
            WHERE block.page IS NULL");
        return $query->getResult();
    }
}