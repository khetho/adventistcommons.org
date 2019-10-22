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
		$iStory = 0;
		foreach ($package->getStories() as $storyId => $storyNode) {
			$story = new Story($storyId, $storyNode);
			if (!$story->hasParagraphStyleRange()) {
				continue;
			}
			$sectionId = $this->createSection(
				$productId,
				$story->getName(),
				$iStory,
				$story->getId()
			);
			$iStory++;
			$this->importContents($story, $productId, $sectionId);
		}
	}
	
	private function importContents(Story $story, $productId, $sectionId)
	{
		$iContent = 0;
		foreach ($story->getContents() as $contentId => $content) {
			$this->createProductContent(
				$productId,
				$sectionId,
				$content,
				$iContent,
				$contentId
			);
			$iContent ++;
		}
	}
	
	private function createSection($productId, $name, $order, $idmlId)
	{
		$this->db->insert(
			'product_sections',
			[
				'product_id' => $productId,
				'name' =>       $name,
				'order' =>      $order,
				'idml_id' =>    $idmlId,
			]
		);
		
		return $this->db->insert_id();
	}
	
	private function createProductContent($productId, $sectionId, $content, $order, $idmlId)
	{
		$this->db->insert(
			'product_content',
			[
				'product_id' => $productId,
				'section_id' => $sectionId,
				'content'    => $content,
				'order'      => $order,
				'idml_id'    => $idmlId,
			]
		);
	}
}
