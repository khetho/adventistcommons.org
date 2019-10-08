<?php

namespace AdventistCommons\Export\Idml;

class Idml
{
	private $product;
	private $zipFileName;
	
	public function __construct($zipFileName, $product)
	{
		$this->zipFileName = $zipFileName;
		$this->product = $product;
	}
	
	public function buildFileName()
	{
		return sprintf(
			'%s_%s.idml',
			$this->product['name'],
			date('Y-m-d')
		);
	}
	
	public function getZipContent()
	{
		return file_get_contents($this->zipFileName);
	}
}
