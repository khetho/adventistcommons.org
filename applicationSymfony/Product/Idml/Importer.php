<?php

namespace App\Product\Idml;

use AdventistCommons\Idml\Importer as IdmlImporter;
use AdventistCommons\Idml\HolderBuilder;
use App\Entity\Product;
use App\Product\IdmlUploader;

class Importer
{
    private $importer;
    private $holderBuilder;
    private $uploader;
    
    public function __construct(
        IdmlImporter $importer,
        IdmlUploader $uploader,
        HolderBuilder $holderBuilder
    ) {
        $this->importer = $importer;
        $this->uploader = $uploader;
        $this->holderBuilder = $holderBuilder;
    }
    
    public function import(Product $product): Product
    {
        if (!$product->getIdmlFilename()) {
            return $product;
        }
        $path = $this->uploader->getTargetPath().'/'.$product->getIdmlFilename();
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
