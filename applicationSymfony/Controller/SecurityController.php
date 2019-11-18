<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/admin/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'title' => 'Sign in',
            'user' => null,
            'is_home' => false,
            'breadcrumbs' => [],
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * @Route("/admin/dashboard", name="app_admin")
     */
    public function admin(): Response
    {
        dump('ok');
        return new Response();
    }
}
