<?php

namespace App\Repository;

use App\Entity\Group;
use App\Entity\Paragraph;
use App\Entity\Product;
use App\Entity\Project;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findAdmins(): array
    {
        $adminGroup = $this->getEntityManager()->getRepository(Group::class)->findBy(['name' => 'admin']);

        return $this->createQueryBuilder('u')
            ->where(":group MEMBER OF u.groups")
            ->setParameter('group', $adminGroup)
            ->getQuery()
            ->getResult();
    }

    public function getContributorsForProject(Project $project)
    {
        return $this->createQueryBuilder('u')
            ->innerJoin('u.contentRevisions', 'cr')
            ->where('cr.project = :project')
            ->setParameter('project', $project)
            ->groupBy('u')
            ->setMaxResults(30)
            ->getQuery()
            ->getResult();
    }
}
