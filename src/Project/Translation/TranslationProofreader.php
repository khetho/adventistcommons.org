<?php

namespace App\Project\Translation;

use App\Entity\ContentRevision;
use App\Project\StatusChanger;
use App\Security\Voter\ProjectVoter;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

class TranslationProofreader
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

    public function proofreadTranslation(ContentRevision $contentRevision): bool
    {
        if (!$this->security->isGranted(ProjectVoter::PROOFREAD, $contentRevision->getProject())) {
            return false;
        }
        $contentRevision->proofreadBy($this->security->getUser());
        $this->statusChanger->changeToProofreadIfAllContentProofread($contentRevision->getProject());
        $this->registry->getManager()->flush();

        return true;
    }
}
