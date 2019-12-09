<?php

namespace App\Emailer;

use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\NamedAddress;
use Symfony\Component\Routing\RouterInterface;

class Emailer
{
    private $mailer;
    private $router;
    
    public function __construct(RouterInterface $router, MailerInterface $mailer)
    {
        $this->mailer = $mailer;
        $this->router = $router;
    }
    
    private function buildEmail(User $destinataire): Email
    {
        return (new Email())
            ->from(new NamedAddress('hello@adventistcommons.org', 'adventistcommons'))
            ->to(new NamedAddress($destinataire->getEmail(), $destinataire->getFullName()))
            ;
    }
    
    public function sendAccountActivation($user)
    {
        $activationUrl = $this->router->generate('app_auth_activate', ['code' => $user->getActivationCode()]);
        $email = $this->buildEmail($user)
            ->subject('ac: Account activation')
            ->text('Please follow this link : '.$activationUrl)
            ->html('<p>See Twig integration for better HTML integration!</p>');

        $this->mailer->send($email);
    }
    
    public function sendPasswordResetInvite(User $user)
    {
        $activationUrl = $this->router->generate('app_auth_reset_password', ['code' => $user->getForgottenPasswordCode()]);
        $email = $this->buildEmail($user)
            ->subject('ac: Did you forget your password ?')
            ->text('Please follow this link : '.$activationUrl)
            ->html('<p>See Twig integration for better HTML integration!</p>');

        $this->mailer->send($email);
    }
}
