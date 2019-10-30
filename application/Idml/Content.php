<?php
namespace AdventistCommons\Idml;

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
	
	static function buildUniqueKey($storyKey, $sectionName, $iContent): string
	{
		return sprintf(
			'%s-%s-%s',
			$storyKey,
			$sectionName,
			$iContent
		);
	}
	
	static function extractStoryKey($key): string
	{
		return explode('-', $key)[0];
	}
}
