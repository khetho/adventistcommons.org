<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AccountController
{
    /**
     * @Route("/account", name="app_my_account")
     */
    public function myAccount(Request $request, TokenStorageInterface $tokenStorage)
    {
        throw new \Exception("TODO");
    }
}
