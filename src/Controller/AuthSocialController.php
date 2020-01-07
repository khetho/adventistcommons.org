<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AuthSocialController extends AbstractController
{
    /**
     * Link to this controller to start the "connect" process
     * @param ClientRegistry $clientRegistry
     *
     * @Route("/facebook", name="app_auth_social_facebook")
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function facebookAction(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('facebook')
            ->redirect([
                'public_profile', 'email' // the scopes you want to access
            ])
        ;
    }

    /**
     * After going to Facebook, you're redirected back here
     * because this is the "redirect_route" you configured
     * in config/packages/knpu_oauth2_client.yaml
     *
     * @Route("/facebook/check", name="app_auth_social_facebook_check")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function facebookCheckAction()
    {
        return $this->redirectToRoute('app_about_home');
    }
    
    /**
     * Link to this controller to start the "connect" process
     * @param ClientRegistry $clientRegistry
     *
     * @Route("/google", name="app_auth_social_google")
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function googleAction(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('google')
            ->redirect([
                'public_profile', 'email' // the scopes you want to access
            ])
        ;
    }

    /**
     * After going to Google, you're redirected back here
     * because this is the "redirect_route" you configured
     * in config/packages/knpu_oauth2_client.yaml
     *
     * @Route("/google/check", name="app_auth_social_google_check")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function googleCheckAction()
    {
        return $this->redirectToRoute('app_about_home');
    }
}
