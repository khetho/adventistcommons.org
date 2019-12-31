<?php

namespace App\Product;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class CoverUploader
{
    private $targetDirectory;

    public function __construct(string $targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function upload(Product $product): Product
    {
        $file = $product->getCoverImage();
        if (!$file) {
            return $product;
        }
        
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->targetDirectory, $fileName);
        } catch (FileException $e) {
            // @TODO handle exception if something happens during file upload
        }
        
        if ($product->getCoverImageFilename()) {
            $previousFilename = $this->targetDirectory.'/'.$product->getCoverImageFilename();
            if (file_exists($previousFilename)) {
                unlink($previousFilename);
            }
        }
        
        $product->setCoverImageFilename($fileName);

        return $product;
    }
}
