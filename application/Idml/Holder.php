<?php

namespace AdventistCommons\Idml;

use IDML\Package;
use AdventistCommons\Idml\DomManipulator\StoryBasedOnTags;

/**
 * This class is the nutshell for an Idml package object
 * @package AdventistCommons\Export\Idml
 * @author    Vincent Beauvivre <vincent@beauvivre.fr>
 * @copyright 2019
 */
class Holder
{
	const COPY_KEY = '.ac_idml_ccopy.';
	
	private $project;
	private $product;
	private $zipFileName;
	/** @var Package */
	private $package;
	private $cloned = false;
	private $stories = [];
	private $sections = [];
	
	public function __construct($zipFileName, array $product)
	{
		$this->zipFileName = $zipFileName;
		$this->product = $product;
	}
	
	/**
	 * Set a project is meant to set a translation
	 * Never set a translation without cloning your holder
	 *
	 * @param array $project
	 */
	public function setProject(array $project)
	{
		if ($this->project) {
			throw new \Exception('Cannot change the project. You must clone the holder first if you want antoher language.');
		}
		$this->project = $project;
	}
	
	/**
	 * Clone the holder when you want to set a new translation (project)
	 *
	 * @throws \Exception
	 */
	public function __clone()
	{
		$clearedPreviousName = $this->zipFileName;
		$removedSuffix = '.idml';
		if (substr($clearedPreviousName, -strlen($removedSuffix)) === $removedSuffix) {
			$clearedPreviousName = substr($clearedPreviousName, 0, -strlen($removedSuffix));
		}
		
		$copyKeyPosition = strpos(self::COPY_KEY, $clearedPreviousName);
		if ($copyKeyPosition !== false) {
			$clearedPreviousName = substr($clearedPreviousName, 0, $copyKeyPosition);
		}
		$copyName = $clearedPreviousName.self::COPY_KEY.self::uniqidReal().'.idml';
		copy($this->zipFileName, $copyName);
		$this->zipFileName = $copyName;
		// ensure package is loaded
		$this->getPackage();
		$this->package->setZip($this->zipFileName);
		$this->cloned = true;
	}
	
	public function buildFileName()
	{
		return sprintf(
			'%s_%s_%s.idml',
			$this->product['name'],
			$this->project ? $this->project['language_name'] : 'original',
			date('Y-m-d')
		);
	}
	
	public function getZipContent()
	{
		return file_get_contents($this->zipFileName);
	}
	
	public function getPackage(): Package
	{
		if (!$this->package) {
			$this->package = new Package($this->zipFileName);
		}
		
		return $this->package;
	}
	
	public function getStory($storyKey): Story
	{
		if (!isset($this->stories[$storyKey])) {
			$dom = $this->getPackage()->getStory($storyKey);
			$this->stories[$storyKey] = new Story($storyKey, $dom, StoryBasedOnTags::class);
		}

		return $this->stories[$storyKey];
	}
	
	public function getSections()
	{
		if (!$this->sections) {
			foreach ($this->getPackage()->getStories() as $storyKey => $storyNode) {
				$story = new Story($storyKey, $storyNode, StoryBasedOnTags::class);
				$this->sections = array_merge($this->sections, $story->getSections());
			}
		}

		return $this->sections;
	}

	/**
	 * validate the IDML, by loading all stories, sections and contents. If all loads, it is ok
	 * Exceptions will be thrown if any error occurs
	 */
	public function validate(): void
	{
		$this->getSections();
	}

	private static function uniqidReal($lenght = 13)
	{
		if (function_exists("random_bytes")) {
			$bytes = random_bytes(ceil($lenght / 2));
		} elseif (function_exists("openssl_random_pseudo_bytes")) {
			$bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
		} else {
			throw new \Exception("no cryptographically secure random function available");
		}
		
		return substr(bin2hex($bytes), 0, $lenght);
	}
}
