<?php

namespace App\Product\Idml;

use AdventistCommons\Idml\Importer as IdmlImporter;
use AdventistCommons\Idml\HolderBuilder;
use App\Entity\Product;

class Importer
{
    private $importer;
    private $holderBuilder;

    public function __construct(
        IdmlImporter $importer,
        HolderBuilder $holderBuilder
    ) {
        $this->importer = $importer;
        $this->holderBuilder = $holderBuilder;
    }

    public function import(Product $product, string $targetPath): Product
    {
        if (!$product->getIdmlFilename()) {
            return $product;
        }
        $path = $targetPath.'/'.$product->getIdmlFilename();
        $holder = $this->holderBuilder->buildFromProductArrayAndPath(
            [
                'id'   => $product->getId(),
                'name' => $product->getName(),
            ],
            $path
        );
        $this->importer->import($holder, $product);
        
        return $product;
    }
}
