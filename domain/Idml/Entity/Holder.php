<?php

namespace AdventistCommons\Idml\Entity;

use App\Entity\Product;
use App\Entity\Project;
use IDML\Package;
use AdventistCommons\Idml\DomManipulation\StoryDomManipulator;
use \Exception;

/**
 * This class is the nutshell for an Idml package object
 * @package AdventistCommons\Export\Idml
 * @author    Vincent Beauvivre <vincent@beauvivre.fr>
 * @copyright 2019
 */
class Holder
{
    private $zipFileName;
    private $storyDomManipulator;
    /** @var Product */
    private $product;
    /** @var Project */
    private $project;

    /** @var Package */
    private $package;
    private $stories = [];
    private $sections = [];
    
    public function __construct($zipFileName, StoryDomManipulator $storyDomManipulator, Product $product = null)
    {
        $this->zipFileName = $zipFileName;
        $this->storyDomManipulator = $storyDomManipulator;
        $this->product = $product;
    }

    /**
     * Set a project is meant to set a translation
     * Never set a translation without cloning your holder
     *
     * @param Project $project
     * @throws Exception
     */
    public function setProject(Project $project)
    {
        $this->checkProduct();
        if ($project->getProduct()->getId() !== $this->product->getId()) {
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
            $this->product->getName(),
            $this->project ? $this->project->getLanguage()->getName() : 'original',
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
            $this->stories[$storyKey] = new Story($storyKey, $dom, $this->storyDomManipulator);
        }

        return $this->stories[$storyKey];
    }
    
    public function getStories(): array
    {
        $storiesCount = count($this->getPackage()->getStories());
        if ($storiesCount !== count($this->stories)) {
            foreach ($this->getPackage()->getStories() as $storyKey => $storyNode) {
                $this->stories[$storyKey] = new Story($storyKey, $storyNode, $this->storyDomManipulator);
            }
        }

        return $this->stories;
    }
    
    public function getSections()
    {
        if (!$this->sections) {
            foreach ($this->getPackage()->getStories() as $storyKey => $storyNode) {
                $story = new Story($storyKey, $storyNode, $this->storyDomManipulator);
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
        $errors = [];

        /** @var Story $story */
        foreach ($this->getStories() as $story) {
            try {
                $story->validate();
            } catch (\AdventistCommons\Idml\DomManipulation\Exception $exception) {
                $errors[] = $exception->getMessage();
            }
        }

        if ($errors) {
            throw new \AdventistCommons\Idml\DomManipulation\Exception(implode("\n", $errors));
        }
    }

    private function checkProduct()
    {
        throw new Exception("The holder was created without a product. This action cannot be executed.");
    }
}
