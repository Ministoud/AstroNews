<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ArticleVoter extends Voter
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['ART_EDIT', 'ART_VIEW'])
            && $subject instanceof \App\Entity\Article;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if($this->security->isGranted('ROLE_ADMIN'))
        {
            return true;
        }

        switch ($attribute) {
            case 'ART_EDIT':
                if ($subject->getArtAuthor()->getId() === $user->getId())
                {
                    return true;
                }
                break;
            case 'ART_VIEW':
                if($user)
                {
                    return true;
                }
                break;
        }

        return false;
    }
}
