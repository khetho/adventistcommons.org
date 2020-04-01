<?php

namespace App\Project\Translation;

use App\Entity\ContentRevision;
use App\Entity\Project;
use App\Entity\Sentence;
use App\Project\StatusChanger;
use App\Security\Voter\ProjectVoter;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

class TranslationReviewer
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

    public function reviewTranslation(ContentRevision $contentRevision): bool
    {
        if (!$this->security->isGranted(ProjectVoter::APPROVE, $contentRevision->getProject())) {
            return false;
        }
        $contentRevision->approveBy($this->security->getUser());
        $this->statusChanger->changeToReviewedIfAllContentReviewed($contentRevision->getProject());
        $this->registry->getManager()->flush();
        
        return true;
    }
}
