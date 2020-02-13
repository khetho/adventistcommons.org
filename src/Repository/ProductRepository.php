<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\Project;
use App\Entity\Section;
use App\Entity\Paragraph;
use App\Entity\ProjectContentApproval;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }
    
    public function getContentCount(Product $product): int
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('count(c.id)')
            ->from(Paragraph::class, 'c')
            ->innerJoin('c.section', 's')
            ->where('s.product = :product')
            ->setParameter('product', $product);
        
        return $queryBuilder->getQuery()->getSingleScalarResult();
    }
}
