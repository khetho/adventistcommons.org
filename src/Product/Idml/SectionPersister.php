<?php

namespace App\Product\Idml;

use AdventistCommons\Idml\SectionPersisterInterface;
use App\Entity\Section;
use Doctrine\ORM\EntityManagerInterface;

class SectionPersister implements SectionPersisterInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    public function create(array $data): Section
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
