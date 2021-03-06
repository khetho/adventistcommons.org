<?php
namespace AdventistCommons\Idml\DomManipulation;

use AdventistCommons\Idml\Entity\Story;
use AdventistCommons\Idml\Entity\Section;

interface StoryDomManipulator
{
    public function setRoot(\DOMDocument $root): void;
    
    public function getRoot(): \DOMDocument;
    
    public function getSections(Story $story): array;
    
    public function getContentsForSection(Section $section): array;
    
    public function setContent(Section $section, $searchedKey, $content): void;
    
    /**
     * Validate the
     *
     * @throws Exception
     * @return bool
     */
    public function validate(): bool;
}
