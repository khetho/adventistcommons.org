<?php
namespace AdventistCommons\Idml;

class StoryDomManipulator
{
	const ATTR_PARAGRAPH = 'AppliedParagraphStyle';
	const ATTR_PARAGRAPH_VALUE_DIVIDER = 'ParagraphStyle/Section Divider';
	const TAG_PARAGRAPH_STYLE = 'ParagraphStyleRange';
	const TAG_CHARACTER_STYLE = 'CharacterStyleRange';
	const TAG_CONTENT = 'Content';
	
	private $root;
	private $story;
	private $sections = [];
	
	public function __construct(\DOMDocument $root, Story $story)
	{
		$this->root = $root;
		$this->story = $story;
	}
	
	public function getRoot()
	{
		return $this->root;
	}
	
	public function getSections()
	{
		if (!$this->sections) {
			$catchNext = true;
			$story = $this->getStoryElement();
			// for each paragraph in story
			/** @var \DOMElement $paragraph */
			foreach ($story->getElementsByTagName(self::TAG_PARAGRAPH_STYLE) as $paragraph) {
				if (self::isDivider($paragraph)) {
					// if paragraph is a divider, we want to catch the next one as a new section
					$catchNext = true;
					// but we do not catch the divider
					continue;
				}
				// catch each section once only
				if (!$catchNext) {
					continue;
				}
				$paragraphName = self::extractNameFromParagraph($paragraph);
				/** @var \DOMElement $character */
				foreach ($paragraph->getElementsByTagName(self::TAG_CHARACTER_STYLE) as $character) {
					/** @var \DOMElement $content */
					foreach ($character->getElementsByTagName('Content') as $content) {
						if ($content->nodeValue) {
							// we catch the section only if it has relevant content
							$section = new Section($paragraphName, $this->story);
							$this->sections[$paragraphName] = $section;
							// no catch until next divider
							$catchNext = false;
							continue 3;
						}
					}
				}
			}
		}
		
		return $this->sections;
	}
	
	public function getContentsForSection(Section $section): array
	{
		$contents = [];
		$story = $this->getStoryElement();
		// for each paragraph in story
		/** @var \DOMElement $paragraph */
		$iContent = 0;
		foreach ($story->getElementsByTagName(self::TAG_PARAGRAPH_STYLE) as $paragraph) {
			if (self::isDivider($paragraph)) {
				// if paragraph is a divider, we want to catch the next one as a new section
				$catchNext = true;
				// if contents is here, that means the divider closes the wanted section, so send results !
				if ($contents) {
					return $contents;
				}
			}
			if (!$catchNext) {
				continue;
			}
			$paragraphName = self::extractNameFromParagraph($paragraph);
			// is current section the one we want to catch ?
			if ($paragraphName !== $section->getName()) {
				$catchNext = false;
				continue;
			}
			/** @var \DOMElement $character */
			foreach ($paragraph->getElementsByTagName(self::TAG_CHARACTER_STYLE) as $character) {
				/** @var \DOMElement $content */
				foreach ($character->getElementsByTagName('Content') as $content) {
					if ($content->nodeValue) {
						$contents[$this->buildContentUniqueKey($paragraphName, $iContent)] = $content->nodeValue;
						$iContent++;
					}
				}
			}
		}
		
		return $contents;
	}
	
	public function setContent($sectionName, $key, $content)
	{
		$story = $this->getStoryElement();
		$i = 0;
		/** @var \DOMElement $paragraph */
		foreach ($story->getElementsByTagName(self::TAG_PARAGRAPH_STYLE) as &$paragraph) {
			$paragraphName = self::extractNameFromParagraph($paragraph);
			if ($paragraphName !== $sectionName) {
				continue;
			}
			/** @var \DOMElement $character */
			foreach ($paragraph->getElementsByTagName(self::TAG_CHARACTER_STYLE) as &$character) {
				/** @var \DOMElement $contentNode */
				foreach ($character->getElementsByTagName('Content') as &$contentNode) {
					if ($this->buildContentUniqueKey($paragraphName, $i) === $key) {
						$contentNode->nodeValue = $content;
					}
					$i++;
				}
			}
		}
	}
	
	private function getStoryElement(): \DOMElement
	{
		return $this->root->getElementsByTagName('Story')[0];
	}
	
	private static function extractNameFromParagraph(\DOMElement $paragraph)
	{
		$wholeName = $paragraph->getAttribute(self::ATTR_PARAGRAPH);
		preg_match('#^[a-zA-Z]*/([a-zA-Z ]*) [a-zA-Z]*$#', $wholeName, $matches);
		if (!$matches[1]) {
			throw new \Exception('Cannot find a name for section');
		}
		
		return $matches[1];
	}
	
	private function buildContentUniqueKey($paragraphName, $index)
	{
		return sprintf(
			'%s-%s-%s',
			$this->story->getKey(),
			$paragraphName,
			$index
		);
	}
	
	private static function isDivider(\DOMElement $element)
	{
		return (
			strpos(
				$element->getAttribute(self::ATTR_PARAGRAPH),
				self::ATTR_PARAGRAPH_VALUE_DIVIDER
			) !== false
		);
	}
}
