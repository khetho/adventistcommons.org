<?php
namespace AdventistCommons\Idml\DomManipulation;

use AdventistCommons\Basics\StringFunctions;
use AdventistCommons\Idml\ContentBuilder;
use AdventistCommons\Idml\Entity\Story;
use AdventistCommons\Idml\Entity\Section;

class StoryBasedOnTags implements StoryDomManipulator
{
    const TAG_CONTENT = 'Content';
    const TAG_SECTION = 'XMLElement';
    const ATTR_MARKUP = 'MarkupTag';
    const ATTR_MARKUP_VALUE_EXCLUDED = 'XMLTag/Story';
    
    private $contentBuilder;
    private $root;
    private $sections = [];
    
    public function __construct(ContentBuilder $contentBuilder)
    {
        $this->contentBuilder = $contentBuilder;
    }
    
    public function setRoot(\DOMDocument $root): void
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
            foreach ($sectionsElements as $sectionElement) {
                if (!$sectionElement->getAttribute(self::ATTR_MARKUP)) {
                    return new Exception('Found a section without a name in IDML. Did you tag them all ?');
                }
                if ($sectionElement->getAttribute(self::ATTR_MARKUP) !== self::ATTR_MARKUP_VALUE_EXCLUDED) {
                    $sectionName = explode('/', $sectionElement->getAttribute(self::ATTR_MARKUP))[1];
                    if (isset($this->sections[$sectionName])) {
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
        foreach ($contentElements as $contentElement) {
            if ($contentElement->nodeValue) {
                $contents[] = $this->contentBuilder->build($iContent, $contentElement, $section);
                $iContent++;
            }
        }
        
        return $contents;
    }
    
    public function setContent(Section $section, $searchedKey, $newContent): void
    {
        $iContent = 0;
        $storyKey = $this->contentBuilder->extractStoryKey($searchedKey);
        foreach ($section->getSectionElement()->getElementsByTagName(self::TAG_CONTENT) as $contentElement) {
            if ($contentElement->nodeValue) {
                if ($this->contentBuilder->buildUniqueKey($storyKey, $section->getName(), $iContent) === $searchedKey) {
                    $contentElement->nodeValue = $newContent;
                    return;
                }
                $iContent++;
            }
        }
    }
    
    public function validate(): bool
    {
        $xpath = new \DOMXPath($this->getRoot());
        $errors = [];
        
        $appliedCharacterStyles = [
            'Text',
            '$ID/[No character style]',
        ];
        foreach ($appliedCharacterStyles as $appliedCharacterStyle) {
            $query = sprintf(
                '//CharacterStyleRange[@AppliedCharacterStyle="CharacterStyle/%s"]/following-sibling::CharacterStyleRange[@AppliedCharacterStyle="CharacterStyle/%s"]',
                $appliedCharacterStyle,
                $appliedCharacterStyle
            );
            /** @var \DOMNodeList $entries */
            $entries = $xpath->query($query);
            /** @var \DOMElement $entry */
            foreach ($entries as $entry) {
                if (!$entry->textContent) {
                    continue;
                }
                $errors[] = sprintf('Two following paragraphs have the same style : «%s».', StringFunctions::limit($entry->textContent), 30);
            }
        }
        
        if ($errors) {
            throw new Exception(implode("\n", $errors));
        }
        
        return true;
    }
}
