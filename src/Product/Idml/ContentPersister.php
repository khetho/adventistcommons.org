<?php

namespace App\Product\Idml;

use AdventistCommons\Idml\ContentPersisterInterface;
use App\Entity\Content;
use Doctrine\ORM\EntityManagerInterface;

class ContentPersister implements ContentPersisterInterface
{
    private $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    public function create(array $data): Content
    {
        $content = new Content();
        $content->setSection($data['section']);
        $content->setContent($data['content']);
        $content->setOrder($data['order']);
        $content->setContentKey($data['key']);
        $this->entityManager->persist($content);
        
        return $content;
    }
}
