<?php
namespace AdventistCommons\Idml\Entity;

class Section
{
    private $story;
    private $name;
    private $sectionElement;
    private $dbId;
    private $contents;
    
    public function __construct($name, Story $story, \DOMElement $sectionElement = null)
    {
        $this->story = $story;
        $this->name = $name;
        $this->sectionElement = $sectionElement;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function getStory(): Story
    {
        return $this->story;
    }
    
    public function getKey(): string
    {
        return sprintf('%s-%s', $this->story->getKey(), md5($this->name));
    }
    
    public function setDbId($id): void
    {
        $this->dbId = $id;
    }

    public function getDbId()
    {
        return $this->dbId;
    }
    
    public function getSectionElement(): \DOMElement
    {
        return $this->sectionElement;
    }

    public function getContents(): array
    {
        if (!$this->contents) {
            $this->contents = $this->story->getDomManipulator()->getContentsForSection($this);
        }

        return $this->contents;
    }

    public function setContent($key, $newContent): void
    {
        $this->getStory()->getDomManipulator()->setContent($this, $key, $newContent);
    }
}
