<?php
namespace AdventistCommons\Idml;

use AdventistCommons\Idml\Entity\Content;
use AdventistCommons\Idml\Entity\Section;

class ContentBuilder
{
    public function build($iContent, \DOMElement $contentNode, Section $section)
    {
        return new Content(
            $this->buildUniqueKey($section->getStory()->getKey(), $section->getName(), $iContent),
            $contentNode,
            $section
        );
    }
    
    public static function buildUniqueKey($storyKey, $sectionName, $iContent): string
    {
        return sprintf(
            '%s-%s-%s',
            $storyKey,
            $sectionName,
            $iContent
        );
    }
    
    public static function extractStoryKey($key): string
    {
        return explode('-', $key)[0];
    }
}
