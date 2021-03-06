<?php

namespace App\Repository;

use App\Entity\Section;
use App\Entity\Paragraph;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class SectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Section::class);
    }
    
    public function getContentCount(Section $section): int
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('count(c.id)')
            ->from(Paragraph::class, 'c')
            ->where('c.section = :section')
            ->setParameter('section', $section);
        
        return $queryBuilder->getQuery()->getSingleScalarResult();
    }
}
