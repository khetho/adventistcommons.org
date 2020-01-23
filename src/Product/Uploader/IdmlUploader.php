<?php

namespace App\Product\Uploader;

use App\Entity\Product;
use App\Product\Idml\Importer;
use App\Form\UploaderInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use \Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class IdmlUploader implements UploaderInterface
{
    private $targetDirectory;
    private $importer;

    public function __construct(string $targetDirectory, Importer $importer)
    {
        $this->targetDirectory = $targetDirectory;
        $this->importer = $importer;
    }

    public function upload(Product $product)
    {
        $file = $product->getIdmlFile();
        if (!$file) {
            return $product;
        }

        if ($product->getIdmlFilename()) {
            throw new Exception('It is not allowed to change the Idml file');
        }

        if ($file instanceof UploadedFile) {
            throw new Exception('It is not allowed to upload a file that is not an UploadedFile object');
        }

        /** @var UploadedFile $file */
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.idml';

        try {
            $file->move($this->targetDirectory, $fileName);
        } catch (FileException $e) {
            // @TODO handle exception if something happens during file upload
        }
        
        $product->setIdmlFilename($fileName);
        $product = $this->importer->import($product, $this->targetDirectory);

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
