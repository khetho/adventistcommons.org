<?php
namespace AdventistCommons\Idml;

class Content
{
	private $paragraphName;
	private $iContent;
	private $content;
	private $section;
	
	public function __construct($sectionName, $iContent, $content, Section $section)
	{
		$this->sectionName = $sectionName;
		$this->iContent = $iContent;
		$this->content = $content;
		$this->section = $section;
	}
	
	public function getKey(): string
	{
		return self::buildUniqueKey(
			$this->section->getStory()->getKey(),
			$this->sectionName,
			$this->iContent
		);
	}
	
	public function getContent()
	{
		return $this->content;
	}
	
	static function buildUniqueKey($storyKey, $sectionName, $iContent)
	{
		return sprintf(
			'%s-%s-%s',
			$storyKey,
			$sectionName,
			$iContent
		);
	}
	
	static function extractStoryKey($key)
	{
		return explode('-', $key)[0];
	}
}
