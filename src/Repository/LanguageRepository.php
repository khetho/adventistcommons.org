<?php

namespace App\Repository;

use App\Entity\Language;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

class LanguageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Language::class);
    }
    
    public function findUsedInProject(): array
    {
        return $this
            ->findUsedInProjectQueryBuilder()
            ->getQuery()
            ->getResult();
    }

    public function findUsedInEnabledProject(): array
    {
        return $this
            ->findUsedInEnabledProjectQueryBuilder()
            ->getQuery()
            ->getResult();
    }

    public function findUsedInProjectQueryBuilder(): QueryBuilder
    {
        return $this
            ->createQueryBuilder('l')
            ->innerJoin('l.projects', 'j')
            ->groupBy('l');
    }

    public function findUsedInEnabledProjectQueryBuilder(): QueryBuilder
    {
        return $this
            ->findUsedInProjectQueryBuilder()
            ->andWhere('j.enabled = :project_enabled')
            ->setParameter('project_enabled', true)
            ->innerJoin('j.product', 'p')
            ->andWhere('p.enabled = :product_enabled')
            ->setParameter('product_enabled', true)
        ;
    }
}
