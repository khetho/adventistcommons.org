<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\Sentence;
use App\Entity\User;
use App\Entity\Project;
use App\Entity\Section;
use App\Entity\ContentRevision;
use DateInterval;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;
use Exception;

class ContentRevisionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContentRevision::class);
    }

    public function getUserReport(User $user, string $role)
    {
        $this->checkRole($role);
        $queryBuilder = $this->createQueryBuilder('cr')
            ->select(
                'd.name as product_name,
                l.name as language_name,
                count(distinct(st1.id)) as trans_count,
                max(cr.createdAt) as last_date,
                count(distinct(st2.id)) as total_count'
            )
            ->innerJoin('cr.project', 'j')
            ->innerJoin('cr.sentence', 'st1')
            ->innerJoin('j.product', 'd')
            ->innerJoin('d.sections', 's')
            ->innerJoin('s.paragraphs', 'p')
            ->innerJoin('p.sentences', 'st2')
            ->innerJoin('j.language', 'l')
            ->where(sprintf('cr.%s = :user', $role))
            ->groupBy('d.id, l.id')
            ->setParameter('user', $user);
        
        return $queryBuilder->getQuery()->getResult();
    }

    public function getUserReportPerMonth(User $user, string $role): array
    {
        $this->checkRole($role);
        $sixMonthsAgo = new DateTime('first day of this month');
        $sixMonthsAgo->sub(new DateInterval('P6M'));
        $queryBuilder = $this->createQueryBuilder('cr')
            ->select('count(distinct(s.id)) as month_count, MONTH(cr.createdAt) as month, YEAR(cr.createdAt) AS year, d.name as product_name')
            ->innerJoin('cr.project', 'j')
            ->innerJoin('cr.sentence', 's')
            ->innerJoin('j.product', 'd')
            ->groupBy('month, year, d.id')
            ->orderBy('year, month')
            ->where(sprintf('cr.%s = :user', $role))
            ->setParameter('user', $user)
            ->andWhere('cr.createdAt > :sixMonthsAgo')
            ->setParameter('sixMonthsAgo', $sixMonthsAgo);
            
        $results = [];
        foreach ($queryBuilder->getQuery()->getResult() as $result) {
            $results[$result['product_name']][$result['year']][$result['month']] = $result['month_count'];
        }
        
        return $results;
    }

    private function checkRole(string $role): void
    {
        if (!in_array($role, ['translator', 'proofreader', 'reviewer'])) {
            throw new \Exception(sprintf('A content is not linked to user with a role named «%s»', $role));
        }
    }
    
    /**
     * @param Project $project
     * @param Section $section
     * @return array
     */
    public function getLatestForProjectAndSection(Project $project, Section $section)
    {
        $queryBuilder = $this->createQueryBuilder('cr')
            ->select('s.id as sentence_id', 'cr')
            ->leftJoin(
                ContentRevision::class,
                'cr2',
                Join::WITH,
                'cr2.sentence = cr.sentence AND cr.createdAt < cr2.createdAt'
            )
            ->innerJoin('cr.sentence', 's')
            ->innerJoin('s.paragraph', 'p')
            ->where('p.section = :section')
            ->setParameter('section', $section)
            ->andWhere('cr.project = :project')
            ->setParameter('project', $project)
            ->andWhere('cr2.id IS NULL');

        $results = [];
        foreach ($queryBuilder->getQuery()->getResult() as $result) {
            $results[$result['sentence_id']] = $result[0];
        }
        
        return $results;
    }

    /**
     * @param Sentence $sentence
     * @param Project $project
     * @return ContentRevision|null
     */
    public function findLatestForSentenceAndProject(Sentence $sentence, Project $project): ?ContentRevision
    {
        $queryBuilder = $this->createQueryBuilder('cr')
            ->where('cr.project = :project')
            ->setParameter('project', $project)
            ->andWhere('cr.sentence = :sentence')
            ->setParameter('sentence', $sentence)
            ->orderBy('cr.createdAt', 'DESC')
            ->setMaxResults(1);

        $result = $queryBuilder->getQuery()->getResult();

        return count($result) ? $result[0] : null;
    }
    
    public function findBySentence(Sentence $sentence)
    {
        $queryBuilder = $this->createQueryBuilder('cr')
            ->where('cr.sentence = :sentence')
            ->setParameter('sentence', $sentence)
            ->setMaxResults(30)
            ->orderBy('cr.createdAt', 'DESC');

        return $queryBuilder->getQuery()->getResult();
    }

    public function getCountForStatus(
        Project $project,
        string $status = ContentRevision::STATUS_TRANSLATED,
        Section $section = null
    ): int {
        $status = [$status];
        if ($status === [ContentRevision::STATUS_TRANSLATED]) {
            $status[] = ContentRevision::STATUS_REVIEWED;
            $status[] = ContentRevision::STATUS_PROOFREAD;
        }
        if ($status === [ContentRevision::STATUS_PROOFREAD]) {
            $status[] = ContentRevision::STATUS_REVIEWED;
        }
        $queryBuilder = $this->createQueryBuilder('cr')
            ->select('count(cr.id)')
            ->leftJoin(
                ContentRevision::class,
                'cr2',
                Join::WITH,
                'cr2.sentence = cr.sentence AND cr.createdAt < cr2.createdAt'
            )
            ->innerJoin('cr.sentence', 's')
            ->innerJoin('s.paragraph', 'p')
            ->where('cr.status IN (:status)')
            ->setParameter('status', $status)
            ->andWhere('cr.project = :project')
            ->setParameter('project', $project)
            ->andWhere('cr2.id IS NULL');
        if ($section) {
            $queryBuilder
                ->andWhere('p.section = :section')
                ->setParameter('section', $section);
        }

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    public function countForProduct(Product $product)
    {
        $queryBuilder = $this->createQueryBuilder('cr')
            ->select('count(cr.id)')
            ->innerJoin('cr.project', 'j')
            ->where('j.product = :product')
            ->setParameter('product', $product)
        ;

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }
}
