<?php

namespace App\Repository;

use App\Entity\Language;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class LanguageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Language::class);
    }
    
    public function findUsedInProject()
    {
        return $this
            ->createQueryBuilder('l')
            ->innerJoin('l.projects', 'p')
            ->groupBy('l')
            ->getQuery()
            ->getResult();
    }
}
