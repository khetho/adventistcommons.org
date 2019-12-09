<?php

namespace App\Account;

use AdventistCommons\Basics\StringFunctions;
use App\Entity\User;
use App\Emailer\Emailer;
use Symfony\Component\Routing\RouterInterface;

class Registerer
{
    private $emailer;
    private $router;
    
    public function __construct(Emailer $emailer, RouterInterface $router, StringFunctions $stringFunctions)
    {
        $this->emailer = $emailer;
        $this->router = $router;
        $this->stringFunctions = $stringFunctions;
    }

    public function register(User $user, string $ipAddress): void
    {
        $user->setIpAddress($ipAddress);
        $user->setActive(false);
        $user->setActivationCode($this->stringFunctions->generateString(40));
        $this->emailer->sendAccountActivation($user);
    }
}
