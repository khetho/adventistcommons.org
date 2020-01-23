<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\ContentRevision;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class ContentRevisionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContentRevision::class);
    }
    
    public function getUserReport(User $user)
    {
        $queryBuilder = $this->createQueryBuilder('cr')
            ->select('d.name as product_name, l.name as language_name, count(distinct(s.id)) as trans_count, max(cr.createdAt) as last_date')
            ->innerJoin('cr.project', 'j')
            ->innerJoin('cr.sentence', 's')
            ->innerJoin('j.product', 'd')
            ->innerJoin('j.language', 'l')
            ->where('cr.user = :user')
            ->groupBy('d.id, l.id')
            ->setParameter('user', $user);
        
        return $queryBuilder->getQuery()->getResult();
    }
    
    public function getUserReportPerMounth(User $user): array
    {
        $sixMonthsAgo = new \DateTime('first day of this month');
        $sixMonthsAgo->sub(new \DateInterval('P6M'));
        $queryBuilder = $this->createQueryBuilder('cr')
            ->select('count(distinct(s.id)) as month_count, MONTH(cr.createdAt) as month, YEAR(cr.createdAt) AS year, d.name as product_name')
            ->innerJoin('cr.project', 'j')
            ->innerJoin('cr.sentence', 's')
            ->innerJoin('j.product', 'd')
            ->groupBy('month, year, d.id')
            ->orderBy('year, month')
            ->where('cr.user = :user')
            ->setParameter('user', $user)
            ->andWhere('cr.createdAt > :sixMonthsAgo')
            ->setParameter('sixMonthsAgo', $sixMonthsAgo);
           
            
        $results = [];
        foreach ($queryBuilder->getQuery()->getResult() as $result) {
            $results[$result['product_name']][$result['year']][$result['month']] = $result['month_count'];
        }
        
        return $results;
    }
}
