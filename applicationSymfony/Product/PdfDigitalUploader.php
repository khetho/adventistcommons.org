<?php

namespace App\Product;

use App\Entity\Product;
use App\Form\UploaderInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class PdfDigitalUploader implements UploaderInterface
{
    private $targetDirectory;

    public function __construct(string $targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function upload(Product $product): Product
    {
        $file = $product->getPdfDigitalFile();
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
        
        if ($product->getPdfDigitalFilename()) {
            $previousFilename = $this->targetDirectory.'/'.$product->getPdfDigitalFilename();
            if (file_exists($previousFilename)) {
                unlink($previousFilename);
            }
        }
        
        $product->setPdfDigitalFilename($fileName);

        return $product;
    }
    
    public function getTargetPath()
    {
        return $this->targetDirectory;
    }
    
    public function handle($data)
    {
        return $data instanceof Product;
    }
}
