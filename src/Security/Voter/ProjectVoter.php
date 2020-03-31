<?php

namespace App\Security\Voter;

use App\Entity\Language;
use App\Entity\Project;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ProjectVoter extends Voter
{
    // these strings are just invented: you can use anything
    const APPROVE = 'approve';
    const REVIEW = 'review';

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::APPROVE, self::REVIEW])) {
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
            case self::APPROVE:
                return $this->canApprove($project, $user);
            case self::REVIEW:
                return $this->canReview($project, $user);
        }

        throw new \LogicException(sprintf('Voter attribute «%s» is not handled for language', $attribute));
    }

    private function canApprove(Project $project, User $user)
    {
        return (
            $project->getApprover() === $user
                ||
            (!$project->getApprover() && $this->canReview($project, $user))
                ||
            (!$project->getApprover() && $user->getLangsHeCanApprove()->contains($project->getLanguage()))
        );
    }

    private function canReview(Project $project, User $user)
    {
        return $user->getLangsHeCanReview()->contains($project->getLanguage());
    }
}
