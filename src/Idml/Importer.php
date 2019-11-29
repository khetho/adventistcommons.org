<?php

namespace AdventistCommons\Idml;

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

    public function import(Holder $holder, $productId)
    {
        $iSection = 0;
        // import sections
        /** @var Section $section */
        foreach ($holder->getSections() as &$section) {
            $sectionId = $this->sectionPersister->create(
                [
                    'product_id' => $productId,
                    'name'       => $section->getName(),
                    'order'      => $iSection,
                    'story_key'  => $section->getStory()->getKey(),
                ]
            );
            $iSection++;
            $section->setDbId($sectionId);
            // import sections’ contents
            $this->importContents($productId, $section);
        }
    }

    private function importContents($productId, Section $section)
    {
        $iContent = 0;
        /** @var Content $content */
        foreach ($section->getContents() as $content) {
            $this->contentPersister->create(
                [
                    'product_id' => $productId,
                    'section_id' => $section->getDbId(),
                    'content'    => $content->getText(),
                    'order'      => $iContent,
                    'key'        => $content->getKey(),
                ]
            );
            $iContent ++;
        }
    }

//    private function createSection($productId, $name, $order, $storyKey)
//    {
//        $this->db->insert(
//            'product_sections',
//            [
//                'product_id' => $productId,
//                'name'       => $name,
//                'order'      => $order,
//                'story_key'  => $storyKey,
//            ]
//        );
//
//        return $this->db->insert_id();
//    }
//
//    private function createProductContent($productId, $sectionId, $content, $order, $idmlId)
//    {
//        $this->db->insert(
//            'product_content',
//            [
//                'product_id'  => $productId,
//                'section_id'  => $sectionId,
//                'content'     => $content,
//                'order'       => $order,
//                'content_key' => $idmlId,
//            ]
//        );
//    }
}
