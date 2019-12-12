<?php

namespace App\Product;

use App\Entity\Product;
use App\Product\Entity\FilterStatus;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Knp\DictionaryBundle\Dictionary\Collection;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Serializer\SerializerInterface;

class FilterApplier
{
    private $currentFilterManager;
    private $manager;

    private $currentFilterStatus;

    public function __construct(
        CurrentFilterManager $currentFilterManager,
        EntityManagerInterface $manager
    ) {
        $this->currentFilterManager = $currentFilterManager;
        $this->manager = $manager;
    }
    
    public function getProducts()
    {
        /** @TODO: filter data */
        $qb = $this->manager->createQueryBuilder();
        $qb->select('p')
            ->from(Product::class, 'p');

        $filterStatus = $this->currentFilterManager->getCurrentFilterStatus();
        $qb = $this->addCriteriaStringWithWildcards($qb, 'name', $filterStatus->getTitle());
        $qb = $this->addCriteriaStringWithWildcards($qb, 'author', $filterStatus->getAuthor());
        $qb = $this->addCriteria($qb, 'type', $filterStatus->getType());
        $qb = $this->addCriteria($qb, 'serie', $filterStatus->getSeries());
        $qb = $this->addCriteriaIn($qb, 'audiences', $filterStatus->getAudience());
        $qb = $this->addCriteria($qb, 'binding', $filterStatus->getBinding());
        $qb = $this->addSort($qb, $filterStatus->getSort());

        return $qb->getQuery();
    }

    private function addCriteria(QueryBuilder $qb, string $property, $value, $operator = '='): QueryBuilder
    {
        if (!$value) {
            return $qb;
        }

        return $qb->andWhere(sprintf('p.%s %s :%s', $property, $operator, $property))
            ->setParameter($property, $value);
    }

    private function addCriteriaIn(QueryBuilder $qb, string $property, $value): QueryBuilder
    {
        if (!$value) {
            return $qb;
        }

        return $qb->andWhere(sprintf(':%s MEMBER OF p.%s', $property, $property))
            ->setParameter($property, $value);
    }

    private function addCriteriaStringWithWildcards(QueryBuilder $qb, string $property, ?string $value): QueryBuilder
    {
        if (!$value) {
            return $qb;
        }

        return $this->addCriteria($qb, $property, '%'.$value.'%', 'LIKE');
    }


    private function addSort(QueryBuilder $qb, string $property): QueryBuilder
    {
        return $qb->orderBy(sprintf('p.%s', $property), 'ASC');
    }
}
