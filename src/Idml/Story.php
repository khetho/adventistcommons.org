<?php
namespace AdventistCommons\Idml;

use AdventistCommons\Idml\DomManipulator\Exception;
use AdventistCommons\Idml\DomManipulator\StoryDomManipulator;

class Story
{
    private $key;
    private $domManipulator;
    private $sections;

    public function __construct($key, \DOMDocument $root, $domManipulatorClass)
    {
        $interfaces = class_implements($domManipulatorClass);
        if (!isset($interfaces[StoryDomManipulator::class])) {
            throw new Exception("Given domManipulatorClass does not implements StoryDomManipulator");
        }

        $this->key = $key;
        $this->domManipulator = new $domManipulatorClass($root);
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
}
