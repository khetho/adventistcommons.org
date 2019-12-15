<?php

namespace App\Product\Idml;

use App\Entity\Product;
use AdventistCommons\Idml\Importer as IdmlImporter;
use AdventistCommons\Idml\Builder;

class Importer implements SectionPersisterInterface
{
    private $importer;
    private $holderBuilder;
    
    public function __construct(
        IdmlImporter $importer,
        Builder $holderBuilder
    ) {
        $this->importer = $importer;
        $this->holderBuilder = $holderBuilder;
    }
    
    public function import(Product $product)
    {
        $holder = $this->builder->
        $this->importer->import();
    }
}
