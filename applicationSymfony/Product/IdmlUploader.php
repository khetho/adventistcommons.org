<?php

namespace App\Product;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;

class IdmlUploader
{
    private $targetDirectory;
    private $filesystem;

    public function __construct(string $targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function upload(Product $product): Product
    {
        $file = $product->getIdmlFile();
        if (!$file) {
            return $product;
        }
        
        if ($product->getIdmlFilename()) {
            throw new \Exception('It is not allowed to change the Idml file');
        }
        
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.idml';

        try {
            $file->move($this->targetDirectory, $fileName);
        } catch (FileException $e) {
            // @TODO handle exception if something happens during file upload
        }
        
        $product->setIdmlFilename($fileName);

        return $product;
    }
    
    public function getTargetPath()
    {
        return $this->targetDirectory;
    }
}
