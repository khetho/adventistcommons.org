<?php

namespace App\Product\Idml;

use AdventistCommons\Idml\ContentPersisterInterface;
use App\Entity\Paragraph;
use App\Entity\Sentence;
use App\Nlp\Client;
use Doctrine\ORM\EntityManagerInterface;

class ContentPersister implements ContentPersisterInterface
{
    private $entityManager;

    private $nlp;

    public function __construct(EntityManagerInterface $entityManager, Client $nlp)
    {
        $this->entityManager = $entityManager;
        $this->nlp = $nlp;
    }
    
    public function create(array $data): Paragraph
    {
        $paragraph = new Paragraph();
        $paragraph->setSection($data['section']);
        $paragraph->setOrder($data['order']);
        $paragraph->setContentKey($data['key']);
        $this->entityManager->persist($paragraph);
        $order = 0;
        foreach ($this->nlp->splitParagraphIntoSentences($data['content']) as $sentenceContent) {
            $sentence = new Sentence();
            $sentence->setContent($sentenceContent);
            $sentence->setOrder($order);
            $sentence->setParagraph($paragraph);
            $this->entityManager->persist($sentence);
            $order++;
        }
        
        return $paragraph;
    }
}
