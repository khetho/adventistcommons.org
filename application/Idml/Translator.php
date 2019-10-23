<?php

namespace AdventistCommons\Idml;
use IDML\Package;

/**
 * Translator for Idml files, to change content of package according to project
 * @package AdventistCommons\Export\Idml
 * @author    Vincent Beauvivre <vincent@beauvivre.fr>
 * @copyright 2019
 */
class Translator
{
	private $db;
	private $productModel;
	private $projectModel;
	
	public function __construct(
		\CI_DB_mysqli_driver $db,
		\Product_model $productModel,
		\Project_model $projectModel
	) {
		$this->db = $db;
		$this->productModel = $productModel;
		$this->projectModel = $projectModel;
	}
	
	public function translate(Holder $baseHolder, array $project): Holder
	{
		$holder = clone($baseHolder);
		$holder->setProject($project);
		$sections = $this->projectModel->getSections( $project['id'] );
		$package = $holder->getPackage();
		foreach ($sections as $section) {
			$storyKey = $section['story_key'];
			$dom = $package->getStory($storyKey);
			$story = new Story($storyKey, $dom);
			$contents = $this->productModel->getSectionContent($project['id'], $section['id']);
			$domManipulator = $story->getDomManipulator();
			foreach ($contents as $content) {
				$domManipulator->setContent($section['name'], $content['content_key'], $content['latest_revision']);
			}
			$package->addStory($domManipulator->getRoot());
		}
		$package->saveAll();
		$holder->setPackage($package);
		
		return $holder;
	}
}
