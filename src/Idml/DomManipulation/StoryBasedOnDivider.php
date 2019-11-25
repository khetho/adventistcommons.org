<?php
namespace AdventistCommons\Idml\DomManipulation;

use AdventistCommons\Idml\Entity\Story;
use AdventistCommons\Idml\Entity\Section;
use AdventistCommons\Idml\Entity\Content;

class StoryBasedOnDivider implements StoryDomManipulator
{
    const ATTR_PARAGRAPH = 'AppliedParagraphStyle';
    const ATTR_PARAGRAPH_VALUE_DIVIDER = 'ParagraphStyle/Section Divider';
    const TAG_PARAGRAPH_STYLE = 'ParagraphStyleRange';
    const TAG_CHARACTER_STYLE = 'CharacterStyleRange';
    const TAG_CONTENT = 'Content';
    
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
            $catchNext = true;
            $storyElement = $this->getStoryElement();
            // for each paragraph in story
            /** @var \DOMElement $paragraph */
            foreach ($storyElement->getElementsByTagName(self::TAG_PARAGRAPH_STYLE) as $paragraph) {
                if (self::isDivider($paragraph)) {
                    // if paragraph is a divider, we want to catch the next one as a new section
                    $catchNext = true;
                    // but we do not catch the divider
                    continue;
                }
                // catch each section once only
                if (!$catchNext) {
                    continue;
                }
                /** @var \DOMElement $character */
                foreach ($paragraph->getElementsByTagName(self::TAG_CHARACTER_STYLE) as $character) {
                    /** @var \DOMElement $content */
                    foreach ($character->getElementsByTagName('Content') as $content) {
                        if ($content->nodeValue) {
                            $paragraphName = self::extractNameFromParagraph($paragraph);
                            // we catch the section only if it has relevant content
                            $section = new Section($paragraphName, $story);
                            $this->sections[$paragraphName] = $section;
                            // no catch until next divider
                            $catchNext = false;
                            continue 3;
                        }
                    }
                }
            }
        }
        
        return $this->sections;
    }
    
    public function getContentsForSection(Section $section): array
    {
        $contents = [];
        $this->foreachContentInSection(
            $section,
            function (Section $section, $iContent, \DOMElement $contentNode) use (&$contents, $section) {
                $contents[] = new Content($iContent, $contentNode, $section);
            }
        );
        
        return $contents;
    }
    
    public function setContent(Section $section, $searchedKey, $newContent): void
    {
        $storyKey = Content::extractStoryKey($searchedKey);
        $this->foreachContentInSection(
            $section,
            function (Section $section, $iContent, \DOMElement $contentNode) use ($newContent, $searchedKey, $storyKey) {
                if (Content::buildUniqueKey($storyKey, $section->getName(), $iContent) === $searchedKey) {
                    $contentNode->nodeValue = $newContent;
                }
            }
        );
    }
    
    private function foreachContentInSection(Section $section, \Closure $action): void
    {
        $storyElement = $this->getStoryElement();
        // for each paragraph in story
        /** @var \DOMElement $paragraph */
        $iContent = 0;
        $catchNext = true;
        $started = false;
        foreach ($storyElement->getElementsByTagName(self::TAG_PARAGRAPH_STYLE) as $paragraph) {
            if (self::isDivider($paragraph)) {
                // if paragraph is a divider, we want to catch the next one as a new section
                $catchNext = true;
                // if it was started, that means the divider closes the wanted section, so send results !
                if ($started) {
                    return;
                }
                continue;
            }
            if (!$catchNext) {
                continue;
            }
            $paragraphName = self::extractNameFromParagraph($paragraph);
            // is current section the one we want to catch ?
            if (!$started && $paragraphName !== $section->getName()) {
                $catchNext = false;
                continue;
            }
            $started = true;
            /** @var \DOMElement $character */
            foreach ($paragraph->getElementsByTagName(self::TAG_CHARACTER_STYLE) as $character) {
                /** @var \DOMElement $content */
                foreach ($character->getElementsByTagName('Content') as $contentNode) {
                    if ($contentNode->nodeValue) {
                        $action($section, $iContent, $contentNode);
                        $iContent++;
                    }
                }
            }
        }
    }
    
    private function getStoryElement(): \DOMElement
    {
        return $this->root->getElementsByTagName('Story')->item(0);
    }
    
    private static function extractNameFromParagraph(\DOMElement $paragraph)
    {
        $wholeName = $paragraph->getAttribute(self::ATTR_PARAGRAPH);
        preg_match('#^[a-zA-Z]*/([a-zA-Z ]*) [a-zA-Z1-9]*$#', $wholeName, $matches);
        if (!isset($matches[1]) || !$matches[1]) {
            throw new Exception(sprintf(
                'Cannot find a name for a section. «%s» attribute is missing or it’s value is not like «ParagraphStyle/section name style.',
                self::ATTR_PARAGRAPH
            ));
        }
        
        return $matches[1];
    }
    
    private static function isDivider(\DOMElement $element)
    {
        return (
            strpos(
                $element->getAttribute(self::ATTR_PARAGRAPH),
                self::ATTR_PARAGRAPH_VALUE_DIVIDER
            ) !== false
        );
    }
}
