<?php

namespace AdventistCommons\Export\Idml;

use IDML\Package;

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
	private $package;
	
	public function __construct($zipFileName, $product)
	{
		$this->zipFileName = $zipFileName;
		$this->product = $product;
	}
	
	public function setProject(array $project)
	{
		$this->project = $project;
	}
	
	public function __clone()
	{
		$this->getPackage();
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
		$this->package->setZip($this->zipFileName);
	}
	
	public function buildFileName()
	{
		return sprintf(
			'%s_%s_%s.idml',
			$this->product['name'],
			$this->project ? $this->project['language'] : 'original',
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
	
	public function setPackage(Package $package)
	{
		$this->package = $package;
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
