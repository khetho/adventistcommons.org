<?php
namespace AdventistCommons\Idml\DomManipulation;

use AdventistCommons\Idml\Entity\Story;
use AdventistCommons\Idml\Entity\Section;
use AdventistCommons\Idml\Entity\Content;

class StoryBasedOnTags implements StoryDomManipulator
{
    const TAG_CONTENT = 'Content';
    const TAG_SECTION = 'XMLElement';
    const ATTR_MARKUP = 'MarkupTag';
    const ATTR_MARKUP_VALUE_EXCLUDED = 'XMLTag/Story';
    
    private $root;
    private $sections = [];
    
    public function __construct(\DOMDocument $root)
    {
        $this->root = $root;
    }
    
    public function getRoot(): \DOMDocument
    {
        return $this->root;
    }
    
    public function getSections(Story $story): array
    {
        if (!$this->sections) {
            $sectionsElements = $this->root->getElementsByTagName(self::TAG_SECTION);
            foreach ($sectionsElements as $sectionKey => $sectionElement) {
                if (!$sectionElement->getAttribute(self::ATTR_MARKUP)) {
                    return new Exception('Found a section without a name in IDML. Did you tag them all ?');
                }
                if ($sectionElement->getAttribute(self::ATTR_MARKUP) !== self::ATTR_MARKUP_VALUE_EXCLUDED) {
                    $sectionName = explode('/', $sectionElement->getAttribute(self::ATTR_MARKUP))[1];
                    if (isset($sections[$sectionName])) {
                        throw new Exception(sprintf('Cannot have many sections with same name : %s', $sectionName));
                    }
                    $this->sections[$sectionName] = new Section($sectionName, $story, $sectionElement);
                }
            }
        }
        
        return $this->sections;
    }
    
    public function getContentsForSection(Section $section): array
    {
        $contents = [];
        $iContent = 0;
        $contentElements = $section->getSectionElement()->getElementsByTagName(self::TAG_CONTENT);
        foreach ($contentElements as $contentKey => $contentElement) {
            if ($contentElement->nodeValue) {
                $contents[] = new Content($iContent, $contentElement, $section);
                $iContent++;
            }
        }
        
        return $contents;
    }
    
    public function setContent(Section $section, $searchedKey, $newContent): void
    {
        $iContent = 0;
        $storyKey = Content::extractStoryKey($searchedKey);
        foreach ($section->getSectionElement()->getElementsByTagName(self::TAG_CONTENT) as $contentElement) {
            if ($contentElement->nodeValue) {
                if (Content::buildUniqueKey($storyKey, $section->getName(), $iContent) === $searchedKey) {
                    $contentElement->nodeValue = $newContent;
                    return;
                }
                $iContent++;
            }
        }
    }
    
    public function validate(): bool
    {
        throw new ValidationException('Idml is not invalid.');        
    }
}
