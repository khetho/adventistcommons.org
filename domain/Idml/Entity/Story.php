<?php
namespace AdventistCommons\Idml\Entity;

use AdventistCommons\Idml\DomManipulation\Exception;
use AdventistCommons\Idml\DomManipulation\StoryDomManipulator;

class Story
{
    private $key;
    /** @var StoryDomManipulator */
    private $domManipulator;
    private $sections;

    public function __construct($key, \DOMDocument $root, StoryDomManipulator $domManipulator)
    {
        $this->key = $key;
        $thisDomManipulator = clone($domManipulator);
        $thisDomManipulator->setRoot($root);
        $this->domManipulator = $thisDomManipulator;
    }
    
    public function getKey(): string
    {
        return $this->key;
    }
    
    public function getSections(): array
    {
        if (!$this->sections) {
            $this->sections = $this->domManipulator->getSections($this);
        }
        return $this->sections;
    }

    public function getSection($sectionName): Section
    {
        return $this->getSections()[$sectionName];
    }

    public function getDomManipulator(): StoryDomManipulator
    {
        return $this->domManipulator;
    }

    public function getDomDocument(): \DOMDocument
    {
        return $this->domManipulator->getRoot();
    }

    public function setContent($sectionName, $key, $newContent) :void
    {
        $section = $this->getSection($sectionName);
        $section->setContent($key, $newContent);
    }
    
    public function validate(): bool
    {
        try {
            $this->domManipulator->validate();
        } catch (Exception $e) {
            $e->setStory($this);
            throw $e;
        }
        
        return true;
    }
}
