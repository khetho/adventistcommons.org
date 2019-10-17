<?php

namespace AdventistCommons\Export\Idml;

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
		if (!$idmlPath || !file_exists($idmlPath)) {
			throw new FileNotFoundException('File do not exists anymore');
		}
		
		$holder = new Holder($idmlPath, $product);
		if ($project) {
			$holder = $this->translator->translate($holder, $project);
		}
		
		return $holder;
	}
}
