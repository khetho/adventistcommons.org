<?php

namespace AdventistCommons\Idml;

use App\Entity\Product;
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
    
    private static $arrAcceptedMimeTypes = [
        'application/zip; charset=binary',
        'application/octet-stream; charset=binary',
    ];
    
    public function __construct(
        StoryDomManipulator $domManipulator
    ) {
        $this->storyDomManipulator = $domManipulator;
    }

    public function buildFromProductArrayAndPath(Product $product, string $idmlPath): Holder
    {
        self::checkFile($idmlPath);
        
        return new Holder($idmlPath, $this->storyDomManipulator, $product);
    }

    public function buildFromPath(string $idmlPath): Holder
    {
        self::checkFile($idmlPath);

        return new Holder($idmlPath, $this->storyDomManipulator);
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
        $mimeType = $fileInfo->file($location);
        if (!in_array($mimeType, self::$arrAcceptedMimeTypes)) {
            throw new FileNotFoundException(sprintf('No correct mimetype, «%s» given', $mimeType));
        }
    }
}
