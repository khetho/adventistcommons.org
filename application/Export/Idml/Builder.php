<?php

namespace AdventistCommons\Export\Idml;

class Builder
{
	private $db;
	private $twig;
	
	public function __construct(\CI_DB_mysqli_driver $db, \Twig_Environment $twig)
	{
		$this->db = $db;
		$this->twig = $twig;
	}
	
	public function buildFromArrayProduct(array $product): Idml
	{
		$zip = new \ZipArchive();
		$path = $this->buildTempPath();
		if (!$zip->open($path, \ZipArchive::CREATE)) {
			throw new \Exception(sprintf('Cannot create Zip at «%s»', $path));
		}
		$zip->addFromString(
			'mimetype',
			'application/vnd.adobe.indesign-idml-package'
		);
		$zip->addFromString(
			'designmap.xml',
			$this->buildDesignMap($product)
		);
		$zip->close();
		
		return new Idml($path, $product);
	}
	
	private function buildDesignMap(array $product)
	{
		return $this->twig->render(
			'export/idml/designmap.xml.twig',
			[
				'product' => $product,
			]
		);
	}
	
	private function buildTempPath()
	{
		$tempFileName = tempnam(sys_get_temp_dir(),'idml_');
		if (file_exists($tempFileName)) {
			if (!unlink($tempFileName)) {
				throw new \Exception(sprintf('Cannot remove previous Zip at «%s»', $tempFileName));
			}
		}
		
		return $tempFileName;
	}
}
