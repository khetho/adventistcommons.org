<?php
namespace AdventistCommons\Idml;

use AdventistCommons\Idml\Entity\Content;
use AdventistCommons\Idml\Entity\Holder;
use AdventistCommons\Idml\Entity\Section;

class Importer
{
    private $dbDriver;

    public function __construct(\CI_DB_mysqli_driver $dbDriver)
    {
        $this->dbDriver = $dbDriver;
    }

    public function import(Holder $holder, $productId)
    {
        $iSection = 0;
        // import sections
        /** @var Section $section */
        foreach ($holder->getSections() as &$section) {
            $sectionId = $this->createSection(
                $productId,
                $section->getName(),
                $iSection,
                $section->getStory()->getKey()
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
            $this->createProductContent(
                $productId,
                $section->getDbId(),
                $content->getText(),
                $iContent,
                $content->getKey()
            );
            $iContent ++;
        }
    }
    
    private function createSection($productId, $name, $order, $storyKey)
    {
        $this->dbDriver->insert(
            'product_sections',
            [
                'product_id' => $productId,
                'name'       => $name,
                'order'      => $order,
                'story_key'  => $storyKey,
            ]
        );
        
        return $this->dbDriver->insert_id();
    }
    
    private function createProductContent($productId, $sectionId, $content, $order, $idmlId)
    {
        $this->dbDriver->insert(
            'product_content',
            [
                'product_id'  => $productId,
                'section_id'  => $sectionId,
                'content'     => $content,
                'order'       => $order,
                'content_key' => $idmlId,
            ]
        );
    }
}
