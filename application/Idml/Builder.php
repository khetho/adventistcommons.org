<?php

namespace AdventistCommons\Idml;

/**
 * Class Able to build some Idml holder
 * @package AdventistCommons\Export\Idml
 * @author    Vincent Beauvivre <vincent@beauvivre.fr>
 * @copyright 2019
 */
class Builder
{
	private $db;
	private $twig;
	private $translator;
	
	private static $arr_accepted_mime_types = [
		'application/zip; charset=binary',
		'application/octet-stream; charset=binary',
	];
	
	public function __construct(
		\CI_DB_mysqli_driver $db,
		\Twig_Environment $twig,
		Translator $translator
	) {
		$this->db = $db;
		$this->twig = $twig;
		$this->translator = $translator;
	}
	
	public function buildFromArrayProductAndProject(array $product, array $project = null): Holder
	{
		if ($project && $product['id'] !== $project['product_id']) {
			throw new \LogicException('Consistence error in product and project');
		}
		if (!$product['idml_file']) {
			return null;
		}
		$idmlPath = realpath(sprintf(
			'%s/../../../uploads/%s.idml',
			__DIR__,
			$product['idml_file']
		));
		self::checkFile($idmlPath);
		
		$holder = new Holder($idmlPath, $product);
		if ($project) {
			$holder = $this->translator->translate($holder, $project);
		}
		
		return $holder;
	}
	
	public function buildFromProductAndPath(array $product, string $idmlPath)
	{
		self::checkFile($idmlPath);
		
		return new Holder($idmlPath, $product);
	}
	
	/**
	 * @param $idmlPath
	 * @throws FileNotFoundException
	 */
	private static function checkFile(string $idmlPath): void
	{
		if (!$idmlPath || !file_exists($idmlPath) || !self::checkMimeType($idmlPath)) {
			throw new FileNotFoundException('File do not exists anymore');
		}
	}
	
	/**
	 * Checks the mimetype of the IDML file
	 *
	 * @param $location
	 * @return bool
	 * @throws \Exception
	 */
	private static function checkMimeType($location)
	{
		$fileInfo = new \finfo(FILEINFO_MIME);
		$mime = $fileInfo->file($location);
		
		if(in_array($mime, self::$arr_accepted_mime_types))
		{
			return true;
		}else{
			throw new \Exception('No correct mimetype');
		}
	}
}
