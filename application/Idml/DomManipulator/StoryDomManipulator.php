<?php
namespace AdventistCommons\Idml\DomManipulator;

use AdventistCommons\Idml\Story;
use AdventistCommons\Idml\Section;
use AdventistCommons\Idml\Content;

interface StoryDomManipulator
{	
	public function getRoot(): \DOMDocument;
	
	public function getSections(Story $story): array;
	
	public function getContentsForSection(Section $section): array;
	
	public function setContent(Section $section, $searchedKey, $content): void;
}
