<?php

namespace App\Security;

use App\Entity\Language;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class LanguageVoter extends Voter
{
    // these strings are just invented: you can use anything
    const APPROVE = 'approve';
    const REVIEW = 'review';

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::APPROVE, self::REVIEW])) {
            return false;
        }
        if (!$subject instanceof Language) {
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

        /** @var Language $language */
        $language = $subject;

        switch ($attribute) {
            case self::APPROVE:
                return $this->canApprove($language, $user);
            case self::REVIEW:
                return $this->canReview($language, $user);
        }

        throw new \LogicException(sprintf('Voter attribute «%s» is not handled for language', $attribute));
    }

    private function canApprove(Language $language, User $user)
    {
        return $this->canReview($language, $user) || $user->getLangsHeCanApprove()->contains($language);
    }

    private function canReview(Language $language, User $user)
    {
        return $user->getLangsHeCanReview()->contains($language);
    }
}
