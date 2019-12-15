<?php

namespace App\Product\Idml;

use AdventistCommons\Idml\ContentPersisterInterface;
use App\Entity\Content;
use App\Entity\Section;
use Doctrine\ORM\EntityManagerInterface;

class ContentPersister implements ContentPersisterInterface
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    public function create(array $data)
    {
        $content = new Content();
        $content->setSection($data['section']);
        $content->setContent($data['content']);
        $content->setOrder($data['order']);
        $content->setContentKey($data['key']);
        $this->entityManager->persist($content);
    }
}
