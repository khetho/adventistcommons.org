<?php
namespace AdventistCommons\Idml;

class Section
{
	private $story;
	private $name;
	private $dbId;
	private $contents;
	
	public function __construct($name, Story $story)
	{
		$this->story = $story;
		$this->name = $name;
	}
	
	public function getName(): string
	{
		return $this->name;
	}
	
	public function getStory(): Story
	{
		return $this->getStory();
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
	
	public function getContents(): array
	{
		if (!$this->contents) {
			$this->contents = $this->story->getDomManipulator()->getContentsForSection($this);
		}
		
		return $this->contents;
	}
	
	public function getContentByKey($key)
	{
		$this->getContents();
		
		return $this->contents[$key];
	}
}
