<?php

namespace App\Product\Idml;

use AdventistCommons\Idml\HolderBuilder;

class Validator
{
    private $holderBuilder;
    
    public function __construct(
        HolderBuilder $holderBuilder
    ) {
        $this->holderBuilder = $holderBuilder;
    }
    
    public function validate(string $path)
    {
        $holder = $this->holderBuilder->buildFromPath($path);
        $holder->validate();
    }
}
