<?php
namespace AdventistCommons\Idml;

class Story
{
	private $key;
	private $domManipulator;
	private $sections;

	public function __construct($key, \DOMDocument $root)
	{
		$this->key = $key;
		$this->domManipulator = new StoryDomManipulator($root, $this);
	}
	
	public function getKey(): string
	{
		return $this->key;
	}
	
	public function getSections(): array
	{
		if (!$this->sections) {
			$this->sections = $this->domManipulator->getSections();
		}
		return $this->sections;
	}
	
	public function getDomManipulator()
	{
		return $this->domManipulator;
	}
	
	public function getDom()
	{
		return $this->domManipulator->getRoot();
	}
}
