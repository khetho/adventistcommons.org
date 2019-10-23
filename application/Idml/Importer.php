<?php
namespace AdventistCommons\Idml;

use IDML\Package;

class Importer
{
	private $db;

	public function __construct(\CI_DB_mysqli_driver $db)
	{
		$this->db = $db;
	}

	public function import(Package $package, $productId)
	{
		$sections = [];
		// gather all sections in one array
		foreach ($package->getStories() as $storyId => $storyNode) {
			$story = new Story($storyId, $storyNode);
			$sections = array_merge($sections, $story->getSections());
		}
		$iSection = 0;
		// import sections
		/** @var Section $section */
		foreach ($sections as &$section) {
			$sectionId = $this->createSection(
				$productId,
				$section->getName(),
				$iSection,
				$section->getStory()->getKey()
			);
			$iSection++;
			$section->setDbId($sectionId);
			// import sections’ contents
			$this->importContents($productId, $section);
		}
	}
	
	private function importContents($productId, Section $section)
	{
		$iContent = 0;		
		/** @var Content $content */
		foreach ($section->getContents() as $content) {
			$this->createProductContent(
				$productId,
				$section->getDbId(),
				$content->getContent(),
				$iContent,
				$content->getKey()
			);
			$iContent ++;
		}
	}
	
	private function createSection($productId, $name, $order, $storyKey)
	{
		$this->db->insert(
			'product_sections',
			[
				'product_id' => $productId,
				'name'       => $name,
				'order'      => $order,
				'story_key'  => $storyKey,
			]
		);
		
		return $this->db->insert_id();
	}
	
	private function createProductContent($productId, $sectionId, $content, $order, $idmlId)
	{
		$this->db->insert(
			'product_content',
			[
				'product_id'  => $productId,
				'section_id'  => $sectionId,
				'content'     => $content,
				'order'       => $order,
				'content_key' => $idmlId,
			]
		);
	}
}
