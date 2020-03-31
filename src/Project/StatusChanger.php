<?php

namespace App\Project;

use App\Entity\ContentRevision;
use App\Entity\Product;
use App\Entity\Project;
use App\Entity\Sentence;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Workflow\Registry;

class StatusChanger
{
    private $workflowRegistry;
    private $doctrineRegistry;

    public function __construct(ManagerRegistry $registry, Registry $workflowRegistry)
    {
        $this->workflowRegistry = $workflowRegistry;
        $this->doctrineRegistry = $registry;
    }

    public function changeToTranslatedIfAllContentTranslated(Project $project): void
    {
        $this->changeIfAllContentHasStatus($project, ContentRevision::STATUS_TRANSLATED, 'declare_translated');
    }

    public function changeToApprovedIfAllContentApproved(Project $project): void
    {
        $this->changeIfAllContentHasStatus($project, ContentRevision::STATUS_APPROVED, 'declare_approved');
    }

    private function changeIfAllContentHasStatus(Project $project, string $contentStatus, string $transition): void
    {
        $workflow = $this->workflowRegistry->get($project);
        $countTranslated = $this->doctrineRegistry
            ->getRepository(ContentRevision::class)
            ->getCountForStatus($project, $contentStatus);
        $countSentences = $this->doctrineRegistry
            ->getRepository(Sentence::class)
            ->getCountForProduct($project->getProduct());
        if ($countTranslated >= $countSentences) {
            $workflow->apply($project, $transition);
        }
    }

    public function startIfUndone(Project $project)
    {
        if ($project->getStatus() === Project::STATUS_TRANSLATABLE) {
            $workflow = $this->workflowRegistry->get($project);
            $workflow->apply($project, 'start');
        }
    }
}
