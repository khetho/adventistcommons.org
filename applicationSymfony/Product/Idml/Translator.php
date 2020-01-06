<?php

namespace App\Product\Idml;

use AdventistCommons\Idml\Entity\Holder;
use AdventistCommons\Idml\HolderBuilder;
use AdventistCommons\Idml\Translator as IdmlTranslator;
use App\Entity\Project;
use App\Product\Uploader\IdmlUploader;

class Translator
{
    private $holderBuilder;
    private $translator;
    private $idmlUploader;

    public function __construct(
        HolderBuilder $holderBuilder,
        IdmlTranslator $translator,
        IdmlUploader $idmlUploader
    ) {
        $this->holderBuilder = $holderBuilder;
        $this->translator = $translator;
        $this->idmlUploader = $idmlUploader;
    }

    public function translate(Project $project): Holder
    {
        $product = $project->getProduct();
        $holder = $this->holderBuilder->buildFromProductArrayAndPath(
            $product,
            $this->idmlUploader->getTargetPath().'/'.$product->getIdmlFilename()
        );
        $holder = $this->translator->translate($holder, $project);

        return $holder;
    }
}
