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
		foreach ($package->getStories() as $storyId => $storyNode) {
			$stories[$storyId] = new Story($storyId, $storyNode);
			$sections = array_merge($sections, $stories[$storyId]->getSections());
		}
		$iSection = 0;
		foreach ($sections as &$section) {
			$sectionId = $this->createSection(
				$productId,
				$section->getName(),
				$iSection,
				$section->getId(),
				$section->getStory()->getId()
			);
			$iSection++;
			$section->setDbId($sectionId);
			$this->importContents($productId, $section);
		}
	}
	
	private function importContents($productId, Section $section)
	{
		$iContent = 0;		
		foreach ($section->getStory()->getContentsBySection($section) as $contentId => $content) {
			$this->createProductContent(
				$productId,
				$section->getDbId(),
				$content,
				$iContent,
				$contentId
			);
			$iContent ++;
		}
	}
	
	private function createSection($productId, $name, $order, $sectionId, $storyId)
	{
		$this->db->insert(
			'product_sections',
			[
				'product_id' => $productId,
				'name'       => $name,
				'order'      => $order,
				'story_key'   => $storyId,
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
