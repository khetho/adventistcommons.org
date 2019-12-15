<?php

namespace App\Product\Idml;

use AdventistCommons\Idml\SectionPersisterInterface;
use App\Entity\Product;
use App\Entity\Section;
use Doctrine\ORM\EntityManagerInterface;

class SectionPersister implements SectionPersisterInterface
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    public function create(array $data)
    {
        $section = new Section();
        $section->setProduct($data['product']);
        $section->setName($data['name']);
        $section->setOrder($data['order']);
        $section->setStoryKey($data['story_key']);
        $this->entityManager->persist($section);
        
        return $section;
    }
}
