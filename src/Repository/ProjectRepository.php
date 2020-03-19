<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\Language;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }
    
    public function findQueryForLanguage(?Language $language): Query
    {
        $queryBuilder = $this->createQueryBuilder('p');
        if ($language) {
            $queryBuilder->where('p.language = :language')
                ->setParameter('language', $language);
        }
        
        return $queryBuilder->getQuery();
    }

    public function findQueryForLanguages(Collection $languages): Query
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->where('p.language IN (:languages)')
            ->setParameter('languages', $languages);

        return $queryBuilder->getQuery();
    }
}
