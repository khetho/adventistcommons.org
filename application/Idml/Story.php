<?php
namespace AdventistCommons\Idml;

class Story
{
	const ATTR_APPLIED_PARAGRAPH = 'AppliedParagraphStyle';
	const ATTR_VALUE_DIVIDER = 'ParagraphStyle/Section Divider';
	const TAG_PARAGRAPH_STYLE = 'ParagraphStyleRange';
	const TAG_CHARACTER_STYLE = 'CharacterStyleRange';
	const TAG_CONTENT = 'Content';
	
	private $id;
	private $root;

	public function __construct($id, \DOMDocument $root)
	{
		$this->id = $id;
		$this->root = $root;
	}
	
	public function hasParagraphStyleRange(): bool
	{
		return (bool) $this->getFirstParagraphStyleRange();
	}
	
	public function getId(): string
	{
		return $this->id;
	}
	
	public function getSections()
	{
		$sections = [];
		$section = null;		
		$catchNext = true;
		$story = $this->getStoryElement();
		foreach ($story->getElementsByTagName(self::TAG_PARAGRAPH_STYLE) as $paragraph) {
			if (self::isDivider($paragraph)) {
				$catchNext = true;
				continue;
			}
			$name = self::extractName($paragraph);
			if (!$catchNext) {
				continue;
			}
			foreach ($paragraph->getElementsByTagName(self::TAG_CHARACTER_STYLE) as $character) {
				/** @var \DOMElement $content */
				foreach ($character->getElementsByTagName('Content') as $content) {
					if ($content->nodeValue) {
						$section = new Section($name, $this);
						$sections[$name] = $section;
						$catchNext = false;
						continue 3;
					}					
				}
			}
		}		
		
		return $sections;
	}
	
	public function getContentsBySection(Section $section): array
	{
		$story = $this->getStoryElement();
		$contents = [];
		$i = 0;
		foreach ($story->getElementsByTagName(self::TAG_PARAGRAPH_STYLE) as $paragraph) {
			$name = self::extractName($paragraph);
			if ($name !== $section->getName()) {
				continue;
			}
			foreach ($paragraph->getElementsByTagName(self::TAG_CHARACTER_STYLE) as $character) {
				/** @var \DOMElement $content */
				foreach ($character->getElementsByTagName('Content') as $content) {					
					$contents[$this->buildUniqId($content, $i)] = $content->nodeValue;
					$i++;
				}
			}
		}
		
		return $contents;
	}
	
	private function getStoryElement(): \DOMElement
	{
		return $this->root->getElementsByTagName('Story')[0];
	}
	
	private static function extractName(\DOMElement $node)
	{
		$value = $node->getAttribute(self::ATTR_APPLIED_PARAGRAPH);
		preg_match('#^[a-zA-Z]*/([a-zA-Z ]*) [a-zA-Z]*$#', $value, $matches);
		if (!$matches[1]) {
			throw new \Exception('Cannot find a name for section');
		}
		
		return $matches[1];
	}
	
	private function buildUniqId(\DOMElement $node, $key)
	{
		return sprintf(
			'%s-%s-%s',
			$this->getId(),
			$key,
			md5($node->nodeValue)
		);
	}
	
	private static function isDivider(\DOMElement $element)
	{
		return (
			strpos(
				$element->getAttribute(self::ATTR_APPLIED_PARAGRAPH),
				self::ATTR_VALUE_DIVIDER
			) !== false
		);
	}
}
