<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\Language;
use App\Entity\Section;
use App\Entity\ProjectContentApproval;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
    
    public function getApprovedCount(Project $project, Section $section = null): int
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('count(a.id)')
            ->from(ProjectContentApproval::class, 'a')
            ->where('a.project = :project')
            ->setParameter('project', $project);
            
        if ($section) {
            $queryBuilder->innerJoin('a.content', 'c')
                ->andWhere('c.section = :section')
                ->setParameter('section', $section);
        }
        
        return $queryBuilder->getQuery()->getSingleScalarResult();
    }
}
