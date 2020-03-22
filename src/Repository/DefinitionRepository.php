<?php

namespace App\Repository;

use App\Entity\Definition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class DefinitionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Definition::class);
    }
    
    public function findAllIterator()
    {
        return $this
            ->createQueryBuilder('d')
            ->select('d.word', 'd.description')
            ->getQuery()
            ->iterate();
    }
}
