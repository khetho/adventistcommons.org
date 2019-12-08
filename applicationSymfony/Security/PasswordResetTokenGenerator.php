<?php

namespace App\Security;

use AdventistCommons\Basics\Clock;
use AdventistCommons\Basics\StringFunctions;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class PasswordResetTokenGenerator
{
    private $clock;
    
    private $repository;
    
    private $entityManager;
    
    private $stringFunctions;
    
    public function __construct(EntityManagerInterface $entityManager, StringFunctions $stringFunctions, Clock $clock)
    {
        $this->repository = $entityManager->getRepository(User::class);
        $this->entityManager = $entityManager;
        $this->stringFunctions = $stringFunctions;
        $this->clock = $clock;
    }

    public function treatEmail(string $email)
    {
        $user = $this->repository->findOneBy(['email' => $email]);
        $user->setForgottenPasswordCode($this->stringFunctions->generateString(20));
        $user->setForgottenPasswordTime($this->clock->now());
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        // @todo : sendemail
    }
}
