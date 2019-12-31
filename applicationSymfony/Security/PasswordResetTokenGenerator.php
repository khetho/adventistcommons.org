<?php

namespace App\Security;

use AdventistCommons\Basics\Clock;
use AdventistCommons\Basics\StringFunctions;
use App\Email\SenderToUser;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class PasswordResetTokenGenerator
{
    private $clock;
    private $entityManager;
    private $stringFunctions;
    private $emailSender;
    
    public function __construct(
        EntityManagerInterface $entityManager,
        StringFunctions $stringFunctions,
        Clock $clock,
        SenderToUser $emailSender
    ) {
        $this->entityManager = $entityManager;
        $this->stringFunctions = $stringFunctions;
        $this->clock = $clock;
        $this->emailSender = $emailSender;
    }

    public function treatEmail(string $email)
    {
        $repository = $this->entityManager->getRepository(User::class);
        $user = $repository->findOneBy(['email' => $email]);
        $user->setForgottenPasswordCode($this->stringFunctions->generateString(20));
        $user->setForgottenPasswordTime($this->clock->now());
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $this->emailSender->sendPasswordResetInvite($user);
    }
}
