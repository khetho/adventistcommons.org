<?php

namespace App\Product;

use App\Entity\Attachment;
use App\Entity\Product;
use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AttachmentUploader
{
    private $targetDirectory;
    private $entityManager;

    public function __construct(string $targetDirectory, EntityManagerInterface $entityManager)
    {
        $this->targetDirectory = $targetDirectory;
        $this->entityManager = $entityManager;
    }

    public function upload(Attachment $attachment, Product $product): Attachment
    {
        $project = $this->entityManager->getRepository(Project::class)->findOneBy([
            'language' => $attachment->getLanguage(),
            'product' => $product,
        ]);
        if (!$project) {
            $project = new Project();
            $project->setProduct($product);
            $project->setLanguage($attachment->getLanguage());
        }
        $attachment->setProject($project);
        
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
        $attachment->setFilename($fileName);

        return $attachment;
    }
    
    public function getTargetPath()
    {
        return $this->targetDirectory;
    }
}
