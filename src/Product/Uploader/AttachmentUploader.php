<?php

namespace App\Product\Uploader;

use App\Entity\Attachment;
use App\Entity\Product;
use App\Entity\Project;
use App\Form\UploaderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AttachmentUploader implements UploaderInterface
{
    private $targetDirectory;
    private $entityManager;

    public function __construct(string $targetDirectory, EntityManagerInterface $entityManager)
    {
        $this->targetDirectory = $targetDirectory;
        $this->entityManager = $entityManager;
    }

    public function upload(Attachment $attachment): Attachment
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
        $attachment->setFileName($fileName);

        return $attachment;
    }
    
    public function getTargetPath()
    {
        return $this->targetDirectory;
    }
    
    public function handle($data)
    {
        return $data instanceof Attachment;
    }
}
