<?php

namespace App\TranslationMemory;

use App\Entity\ContentRevision;
use App\Entity\Project;
use App\Repository\ContentRevisionRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProjectIndexer
{
    private $indexer;
    private $registry;

    public function __construct(Indexer $indexer, ManagerRegistry $registry)
    {
        $this->indexer = $indexer;
        $this->registry = $registry;
    }

    public function index(Project $project)
    {
        /** @var ContentRevisionRepository $repo */
        $repo = $this->registry->getRepository(ContentRevision::class);
        foreach ($repo->getLatestRevisionsForProject($project) as $contentRevisionData) {
            $this->indexer->index(
                'eng', // @TODO : set set language of the source in the Product entity !
                $contentRevisionData['translation'],
                $contentRevisionData['language_code'],
                $contentRevisionData['source']
            );
        }
    }
}
