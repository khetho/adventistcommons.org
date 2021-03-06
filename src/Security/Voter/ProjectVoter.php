<?php

namespace App\Security\Voter;

use App\Entity\Language;
use App\Entity\Project;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class ProjectVoter extends Voter
{
    const PROOFREAD = 'proofread';
    const REVIEW = 'review';
    const DOWNLOAD = 'download';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::PROOFREAD, self::REVIEW, self::DOWNLOAD])) {
            return false;
        }
        if (!$subject instanceof Project) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        /** @var Project $project */
        $project = $subject;

        switch ($attribute) {
            case self::PROOFREAD:
                return $this->canProofread($project, $user);
            case self::REVIEW:
                return $this->canReview($project, $user);
            case self::DOWNLOAD:
                return $this->canDownload($project);
        }

        throw new \LogicException(sprintf('Voter attribute «%s» is not handled for language', $attribute));
    }

    private function canProofread(Project $project, User $user)
    {
        return (
            $project->getProofreader() === $user
                ||
            (!$project->getProofreader() && $this->canReview($project, $user))
                ||
            (!$project->getProofreader() && $user->getLangsHeCanProofread()->contains($project->getLanguage()))
        );
    }

    private function canReview(Project $project, User $user)
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return (
            $project->getReviewer() === $user
            ||
            (!$project->getReviewer() && $user->getLangsHeCanReview()->contains($project->getLanguage()))
        );
    }

    private function canDownload(Project $project)
    {
        $isAdmin = $this->security->isGranted('ROLE_ADMIN');

        return ($project->isDownloadable() || $isAdmin) && count($project->getAttachments());
    }
}
