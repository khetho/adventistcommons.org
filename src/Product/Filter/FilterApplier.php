<?php

namespace App\Product\Filter;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

class FilterApplier
{
    private $currentFilterManager;
    private $manager;
    private $security;
    private $foreignCount = 0;

    public function __construct(
        CurrentFilterManager $currentFilterManager,
        EntityManagerInterface $manager,
        Security $security
    ) {
        $this->currentFilterManager = $currentFilterManager;
        $this->manager = $manager;
        $this->security = $security;
    }
    
    public function getProducts()
    {
        /** @TODO: filter data */
        $qBuilder = $this->manager->createQueryBuilder();
        $qBuilder->select('p')
            ->from(Product::class, 'p');

        $filterStatus = $this->currentFilterManager->getCurrentFilterStatus();
        $qBuilder = $this->addCriteriaStringWithWildcards($qBuilder, 'name', $filterStatus->getTitle());
        $qBuilder = $this->addCriteriaStringWithWildcards($qBuilder, 'author', $filterStatus->getAuthor());
        $qBuilder = $this->addCriteria($qBuilder, 'type', $filterStatus->getType());
        $qBuilder = $this->addForeignCriteria($qBuilder, ['projects', 'language'], $filterStatus->getLanguage());
        $qBuilder = $this->addCriteriaIn($qBuilder, 'audiences', $filterStatus->getAudience());
        $qBuilder = $this->addCriteria($qBuilder, 'binding', $filterStatus->getBinding());
        $qBuilder = $this->addSort($qBuilder, $filterStatus->getSort());
        if (!$this->security->isGranted('ROLE_ADMIN')) {
            $qBuilder = $this->addCriteria($qBuilder, 'enabled', true);
        }

        return $qBuilder->getQuery();
    }

    private function addCriteria(QueryBuilder $qBuilder, string $property, $value, $operator = '='): QueryBuilder
    {
        if (!$value) {
            return $qBuilder;
        }

        return $qBuilder->andWhere(sprintf('p.%s %s :%s', $property, $operator, $property))
            ->setParameter($property, $value);
    }

    private function addCriteriaIn(QueryBuilder $qBuilder, string $property, $value): QueryBuilder
    {
        if (!$value) {
            return $qBuilder;
        }

        return $qBuilder->andWhere(sprintf(':%s MEMBER OF p.%s', $property, $property))
            ->setParameter($property, $value);
    }

    private function addCriteriaStringWithWildcards(QueryBuilder $qBuilder, string $property, ?string $value): QueryBuilder
    {
        if (!$value) {
            return $qBuilder;
        }

        return $this->addCriteria($qBuilder, $property, '%'.$value.'%', 'LIKE');
    }

    private function addForeignCriteria(QueryBuilder $qBuilder, array $path, $value): QueryBuilder
    {
        if (!$value) {
            return $qBuilder;
        }

        $this->foreignCount++;
        $alias = sprintf('f%d', $this->foreignCount);
        $qBuilder->innerJoin(sprintf('p.%s', $path[0]), $alias)
            ->where(sprintf('%s.%s = :%s', $alias, $path[1], $alias))
            ->setParameter($alias, $value);

        return $qBuilder;
    }

    private function addSort(QueryBuilder $qBuilder, string $property): QueryBuilder
    {
        return $qBuilder->orderBy(sprintf('p.%s', $property), 'ASC');
    }
}
