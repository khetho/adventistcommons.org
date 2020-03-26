<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\Language;
use App\Entity\User;
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

    public function findQueryForApprover(User $user): Query
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->where('p.approver = :user')
            ->setParameter('user', $user)
            ->groupBy('p.id');

        return $queryBuilder->getQuery();
    }

    public function findQueryForNotApprover(User $user): Query
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->where('p.language IN (:languages)')
            ->setParameter('languages', $user->getLangsHeCanApprove())
            ->andWhere('p.approver <> :user OR p.approver IS NULL')
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
            ->setParameter('languages', $user->getLangsHeCanApprove())
            ->andWhere('cr.id IS NULL'); // TODO fix this : find product that are not validated by that user only

        return $queryBuilder->getQuery();
    }
}
