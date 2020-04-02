<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\Language;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }
    
    public function findQueryForLanguage(?Language $language = null): Query
    {
        return $this->findQBForLanguage($language)->getQuery();
    }

    public function findQBForLanguage(?Language $language = null): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('j');
        if ($language) {
            $queryBuilder->andWhere('j.language = :language')
                ->setParameter('language', $language);
        }
        
        return $queryBuilder;
    }

    public function findQueryEnabledForLanguage(?Language $language = null): Query
    {
        $queryBuilder = $this->findQBForLanguage($language);
        $queryBuilder->andWhere('j.enabled = :project_enabled')
            ->setParameter('project_enabled', true)
            ->innerJoin('j.product', 'p')
            ->andWhere('p.enabled = :product_enabled')
            ->setParameter('product_enabled', true)
        ;

        return $queryBuilder->getQuery();
    }

    public function findQueryForUserNotTranslator(User $user): Query
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->leftJoin('p.contentRevisions', 'cr')
            ->where('cr.translator = :user')
            ->setParameter('user', $user)
            ->andWhere('p.language IN (:languages)')
            ->setParameter('languages', $user->getAllLanguages())
            ->andWhere('cr.id IS NULL'); // TODO fix this : find product that are not translated by that user only

        return $queryBuilder->getQuery();
    }

    public function findQueryForTranslator(User $user): Query
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->where('p.language IN (:languages)')
            ->innerJoin('p.contentRevisions', 'cr')
            ->where('cr.translator = :user')
            ->setParameter('user', $user)
            ->groupBy('p.id');

        return $queryBuilder->getQuery();
    }

    public function findQueryForProofreader(User $user): Query
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->where('p.proofreader = :user')
            ->setParameter('user', $user)
            ->groupBy('p.id');

        return $queryBuilder->getQuery();
    }

    public function findQueryForNotProofreader(User $user): Query
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->where('p.language IN (:languages)')
            ->setParameter('languages', $user->getLangsHeCanProofread())
            ->andWhere('p.proofreader <> :user OR p.proofreader IS NULL')
            ->setParameter('user', $user);

        return $queryBuilder->getQuery();
    }

    public function findQueryForReviewer(User $user): Query
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->where('p.language IN (:languages)')
            ->innerJoin('p.contentRevisions', 'cr')
            ->where('cr.reviewer = :user')
            ->setParameter('user', $user)
            ->groupBy('p.id');

        return $queryBuilder->getQuery();
    }

    public function findQueryForNotReviewer(User $user): Query
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->leftJoin('p.contentRevisions', 'cr')
            ->where('p.language IN (:languages)')
            ->setParameter('languages', $user->getLangsHeCanProofread())
            ->andWhere('cr.id IS NULL'); // TODO fix this : find product that are not validated by that user only

        return $queryBuilder->getQuery();
    }
}
