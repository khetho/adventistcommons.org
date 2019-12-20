<?php

namespace App\Product;

use App\Entity\Product;
use App\Entity\ProductAttachment;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AttachmentUploader
{
    private $targetDirectory;

    public function __construct(string $targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function upload(ProductAttachment $attachment): Product
    {
        $file = $attachment->getFile();
        if (!$file) {
            return $attachment;
        }
        
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->targetDirectory, $fileName);
        } catch (FileException $e) {
            // @TODO handle exception if something happens during file upload
        }

        return $attachment;
    }
}
