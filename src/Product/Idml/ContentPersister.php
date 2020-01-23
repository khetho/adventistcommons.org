<?php

namespace App\Product\Idml;

use AdventistCommons\Idml\ContentPersisterInterface;
use App\Entity\Paragraph;
use Doctrine\ORM\EntityManagerInterface;

class ContentPersister implements ContentPersisterInterface
{
    private $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    public function create(array $data): Paragraph
    {
        $content = new Paragraph();
        $content->setSection($data['section']);
        $content->setContent($data['content']);
        $content->setOrder($data['order']);
        $content->setContentKey($data['key']);
        $this->entityManager->persist($content);
        
        return $content;
    }
}
