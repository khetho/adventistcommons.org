<?php

namespace AdventistCommons\Idml\Entity;

class Content
{
    private $iContent;
    private $content;
    private $section;
    
    public function __construct($iContent, \DOMElement $content, Section $section)
    {
        $this->iContent = $iContent;
        $this->content = $content;
        $this->section = $section;
    }
    
    public function getKey(): string
    {
        return self::buildUniqueKey(
            $this->section->getStory()->getKey(),
            $this->section->getName(),
            $this->iContent
        );
    }
    
    public function getText(): string
    {
        return $this->content->nodeValue;
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
