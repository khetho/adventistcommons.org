<?php

namespace App\Project\Translation;

use App\Entity\ContentRevision;
use App\Entity\Project;
use App\Entity\Sentence;
use App\Project\StatusChanger;
use App\Security\Voter\ProjectVoter;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

class TranslationApprover
{
    private $registry;
    private $security;
    private $statusChanger;

    public function __construct(ManagerRegistry $registry, Security $security, StatusChanger $statusChanger)
    {
        $this->registry = $registry;
        $this->security = $security;
        $this->statusChanger = $statusChanger;
    }

    public function approveTranslation(ContentRevision $contentRevision): bool
    {
        if (!$this->security->isGranted(ProjectVoter::APPROVE, $contentRevision->getProject())) {
            return false;
        }
        $manager = $this->registry->getManager();
        $contentRevision->approveBy($this->security->getUser());
        $manager->persist($contentRevision);
        $manager->flush();

        $this->statusChanger->approveIfAllContentApproved($contentRevision->getProject());

        return true;
    }
}
