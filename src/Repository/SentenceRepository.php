<?php

namespace App\Repository;

use App\Entity\ContentRevision;
use App\Entity\Product;
use App\Entity\Project;
use App\Entity\Section;
use App\Entity\Sentence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

class SentenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sentence::class);
    }

    public function getSentenceInfo(Project $project, Sentence $sentence): ?array
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('count(cr.id) as cr_count')
            ->from(Sentence::class, 's')
            ->innerJoin(ContentRevision::class, 'cr', Query\Expr\Join::WITH, 'cr.sentence = s')
            ->where('cr.project = :project')
            ->setParameter('project', $project)
            ->andWhere('cr.sentence = :sentence')
            ->setParameter('sentence', $sentence)
        ;

        return $queryBuilder->getQuery()->getResult()[0];
    }

    public function getCountForProduct(Product $product): int
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->select('count(s.id)')
            ->innerJoin('s.paragraph', 'p')
            ->innerJoin('p.section', 'c')
            ->where('c.product = :product')
            ->setParameter('product', $product);

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }


    public function getCountForSection(Section $section): int
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->select('count(s.id)')
            ->innerJoin('s.paragraph', 'p')
            ->where('p.section = :section')
            ->setParameter('section', $section);

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }
}
