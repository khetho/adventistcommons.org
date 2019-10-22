<?php
namespace AdventistCommons\Idml;

class Section
{
	private $story;
	private $name;
	private $dbId;
	
	public function __construct($name, Story $story)
	{
		$this->story = $story;
		$this->name = $name;
	}
	
	public function getStory(): Story
	{
		return $this->story;
	}
	
	public function getName(): string
	{
		return $this->name;
	}
	
	public function getId(): string
	{
		return sprintf('%s-%s', $this->story->getId(), md5($this->name));
	}
	
	public function setDbId($id): void
	{
	 	$this->dbId = $id;
	}

	public function getDbId()
	{
	 	return $this->dbId;
	}
}
