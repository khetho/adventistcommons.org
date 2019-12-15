<?php

namespace AdventistCommons\Idml\Entity;

use IDML\Package;
use \Exception;

/**
 * This class is the nutshell for an Idml package object
 * @package AdventistCommons\Export\Idml
 * @author    Vincent Beauvivre <vincent@beauvivre.fr>
 * @copyright 2019
 */
class Holder
{
    private $project;
    private $product;
    private $zipFileName;
    /** @var Package */
    private $package;
    private $stories = [];
    private $sections = [];
    
    public function __construct($zipFileName, array $product)
    {
        $this->zipFileName = $zipFileName;
        $this->product = $product;
    }
    
    /**
     * Set a project is meant to set a translation
     * Never set a translation without cloning your holder
     *
     * @param array $project
     */
    public function setProject(array $project)
    {
        $this->checkProduct();
        if ($project['product_id'] !== $this->product['id']) {
            throw new Exception('Cannot set the project : this project does not rely on the same product.');
        }
        if ($this->project) {
            throw new Exception('Cannot change the project. You must clone the holder first if you want antoher language.');
        }
        $this->project = $project;
    }
    
    public function buildFileName()
    {
        return sprintf(
            '%s_%s_%s.idml',
            $this->product['name'],
            $this->project ? $this->project['language_name'] : 'original',
            date('Y-m-d')
        );
    }
    
    public function getZipContent()
    {
        return file_get_contents($this->zipFileName);
    }
    
    public function getZipFileName()
    {
        return $this->zipFileName;
    }
    
    public function getProduct()
    {
        return $this->product;
    }
    
    public function getPackage(): Package
    {
        if (!$this->package) {
            $this->package = new Package($this->zipFileName);
        }
        
        return $this->package;
    }
    
    public function getStory($storyKey): Story
    {
        if (!isset($this->stories[$storyKey])) {
            $dom = $this->getPackage()->getStory($storyKey);
            $this->stories[$storyKey] = new Story($storyKey, $dom, $this->domManipulator);
        }

        return $this->stories[$storyKey];
    }
    
    public function getStories(): array
    {
        $storiesCount = count($this->getPackage()->getStories());
        if ($storiesCount !== count($this->stories)) {
            foreach ($this->getPackage()->getStories() as $storyKey => $storyNode) {
                $this->stories[$storyKey] = new Story($storyKey, $storyNode, StoryBasedOnTags::class);
            }
        }

        return $this->stories;
    }
    
    public function getSections()
    {
        if (!$this->sections) {
            foreach ($this->getStories() as $storyKey => $storyNode) {
                $story = new Story($storyKey, $storyNode, $this->domManipulator);
                $this->sections = array_merge($this->sections, $story->getSections());
            }
        }

        return $this->sections;
    }

    /**
     * validate the IDML, by loading all stories, sections and contents. If all loads, it is ok
     * Exceptions will be thrown if any error occurs
     */
    public function validate(): void
    {
        /** @var Story $story */
        foreach ($this->getStories() as $story) {
            $story->validate();
        }
        $this->getSections();
    }

    private function checkProduct()
    {
        throw new Exception("The holder was created without a product. This action cannot be executed.");
    }
}
