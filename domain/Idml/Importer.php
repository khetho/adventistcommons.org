<?php

namespace AdventistCommons\Idml;

use App\Entity\Product;
use AdventistCommons\Idml\Entity\Content;
use AdventistCommons\Idml\Entity\Holder;
use AdventistCommons\Idml\Entity\Section;

class Importer
{
    private $sectionPersister;
    private $contentPersister;

    public function __construct(
        SectionPersisterInterface $sectionPersister,
        ContentPersisterInterface $contentPersister
    ) {
        $this->sectionPersister = $sectionPersister;
        $this->contentPersister = $contentPersister;
    }

    public function import(Holder $holder, Product $product)
    {
        $iSection = 0;
        // import sections
        /** @var Section $section */
        foreach ($holder->getSections() as &$section) {
            $sectionObject = $this->sectionPersister->create(
                [
                    'product'   => $product,
                    'name'      => $section->getName(),
                    'order'     => $iSection,
                    'story_key' => $section->getStory()->getKey(),
                ]
            );
            $iSection++;
            $section->setDbObject($sectionObject);
            // import sections’ contents
            $this->importContents($section);
        }
    }

    private function importContents(Section $section)
    {
        $iContent = 0;
        /** @var Content $content */
        foreach ($section->getContents() as $content) {
            $this->contentPersister->create(
                [
                    'section' => $section->getDbObject(),
                    'content' => $content->getText(),
                    'order'   => $iContent,
                    'key'     => $content->getKey(),
                ]
            );
            $iContent ++;
        }
    }
}
