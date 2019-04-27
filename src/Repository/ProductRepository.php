<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\ORM\EntityRepository;

class ProductRepository extends EntityRepository
{

    /**
     * @return Product[]
     */
    public function findNotDeleted()
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.isDeleted = 0')
            ->getQuery()
            ->getResult();
    }
}
