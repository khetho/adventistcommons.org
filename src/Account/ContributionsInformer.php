<?php

namespace App\Account;

use App\Entity\ContentRevision;
use App\Entity\Project;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

class ContributionsInformer
{
    private $registry;

    public function __construct(
        ManagerRegistry $registry
    ) {
        $this->registry = $registry;
    }

    public function getInformations(User $user): array
    {
        $report = [];

        // translations data
        $projectRepo = $this->registry->getRepository(Project::class);
        $contentRepo = $this->registry->getRepository(ContentRevision::class);

        $report['translator'] = [
            'projects' => $projectRepo->findQueryForTranslator($user)->setMaxResults(10)->getResult(),
            'non-projects' => $projectRepo->findQueryForUserNotTranslator($user)->setMaxResults(10)->getResult(),
            'contributions' => $contentRepo->getUserReport($user, 'translator'),
            'contributions-per-month' => $contentRepo->getUserReportPerMonth($user, 'translator'),
        ];
        $report['proofreader'] = [
            'projects' => $projectRepo->findQueryForProofreader($user)->setMaxResults(10)->getResult(),
            'non-projects' => $projectRepo->findQueryForNotProofreader($user)->setMaxResults(10)->getResult(),
            'contributions' => $contentRepo->getUserReport($user, 'proofreader'),
            'contributions-per-month' => $contentRepo->getUserReportPerMonth($user, 'proofreader'),
        ];
        $report['reviewer'] = [
            'projects' => $projectRepo->findQueryForReviewer($user)->setMaxResults(10)->getResult(),
            'non-projects' => $projectRepo->findQueryForNotReviewer($user)->setMaxResults(10)->getResult(),
            'contributions' => $contentRepo->getUserReport($user, 'reviewer'),
            'contributions-per-month' => $contentRepo->getUserReportPerMonth($user, 'reviewer'),
        ];

        return $report;
    }
}
