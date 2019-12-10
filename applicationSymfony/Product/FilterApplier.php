<?php

namespace App\Product;

use App\Entity\Product;
use App\Product\Entity\FilterStatus;
use Doctrine\ORM\EntityManagerInterface;

class FilterApplier
{
    private $repository;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->repository = $manager->getRepository(Product::class);
    }

    public function getProducts(FilterStatus $filterStatus)
    {
        return $this->repository->findAll();
    }
}
