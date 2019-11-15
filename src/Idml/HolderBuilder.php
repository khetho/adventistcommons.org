<?php

namespace AdventistCommons\Idml;

use AdventistCommons\Idml\Entity\Holder;
use AdventistCommons\Idml\DomManipulation\StoryDomManipulator;
use \LogicException;
use \finfo;

/**
 * Class Able to build some Idml holder
 * @package AdventistCommons\Export\Idml
 * @author    Vincent Beauvivre <vincent@beauvivre.fr>
 * @copyright 2019
 */
class HolderBuilder
{
    private $storyDomManipulator;
    private $uploadPath;
    
    private static $arrAcceptedMimeTypes = [
        'application/zip; charset=binary',
        'application/octet-stream; charset=binary',
    ];
    
    public function __construct(
        StoryDomManipulator $domManipulator,
        $uploadProtectedPath
    ) {
        $this->storyDomManipulator = $domManipulator;
        $this->uploadPath = $uploadProtectedPath;
    }
    
    public function buildFromArrayProduct(array $product): Holder
    {
        if (!$product['idml_file']) {
            return null;
        }
        $idmlPath = realpath(sprintf(
            '%s/%s.idml',
            $this->uploadPath,
            $product['idml_file']
        ));
        self::checkFile($idmlPath);
        
        $holder = new Holder($idmlPath, $product, $this->storyDomManipulator);

        return $holder;
    }
    
    public function buildFromProductAndPath(array $product, string $idmlPath): Holder
    {
        self::checkFile($idmlPath);
        
        return new Holder($idmlPath, $product, $this->storyDomManipulator);
    }
    
    public function buildFromPath(string $idmlPath)
    {
        self::checkFile($idmlPath);
        
        return new Holder($idmlPath);
    }
    
    /**
     * @param $idmlPath
     * @throws FileNotFoundException
     */
    private static function checkFile(string $idmlPath): void
    {
        if (!$idmlPath || !file_exists($idmlPath)) {
            throw new FileNotFoundException('Idml file do not exists anymore');
        }
        self::checkMimeType($idmlPath);
    }
    
    /**
     * Checks the mimetype of the IDML file
     *
     * @param $location
     * @throws \Exception
     */
    private static function checkMimeType($location): void
    {
        $fileInfo = new finfo(FILEINFO_MIME);
        if (!in_array($fileInfo->file($location), self::$arrAcceptedMimeTypes)) {
            throw new FileNotFoundException('No correct mimetype');
        }
    }
}
