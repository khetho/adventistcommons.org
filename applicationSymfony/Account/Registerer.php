<?php

namespace App\Account;

use AdventistCommons\Basics\StringFunctions;
use App\Entity\User;
use App\Email\SenderToUser;
use Symfony\Component\Routing\RouterInterface;

class Registerer
{
    private $emailSender;
    private $router;
    private $stringFunctions;

    public function __construct(
        SenderToUser $emailSender,
        RouterInterface $router,
        StringFunctions $stringFunctions
    ) {
        $this->emailSender = $emailSender;
        $this->router = $router;
        $this->stringFunctions = $stringFunctions;
    }

    public function register(User $user, string $ipAddress): void
    {
        $user->setIpAddress($ipAddress);
        $user->setActive(false);
        $user->setActivationCode($this->stringFunctions->generateString(40));
        $this->emailSender->sendAccountActivation($user);
    }
}
