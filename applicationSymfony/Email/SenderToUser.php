<?php

namespace App\Email;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\NamedAddress;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SenderToUser extends AbstractSender
{
    private function buildEmail(User $recipient): TemplatedEmail
    {
        return (new TemplatedEmail())
            ->from(new NamedAddress('hello@adventistcommons.org', 'Adventist Commons'))
            ->to(new NamedAddress($recipient->getEmail(), $recipient->getFullName()))
            ;
    }

    public function sendAccountActivation(User $user): void
    {
        $activationUrl = $this->router->generate(
            'app_auth_activate',
            ['code' => $user->getActivationCode()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $email = $this->buildEmail($user)
            ->subject('ac: Account activation')
            ->textTemplate('email/user/activate.txt.twig')
            ->htmlTemplate('email/user/activate.html.twig')
            ->context([
                'heading' => 'Account activation',
                'base_url' => $this->getBaseUrl(),
                'user' => $user,
                'activationUrl' => $activationUrl,
            ]);
        $this->send($email);
    }
    
    public function sendPasswordResetInvite(User $user): void
    {
        $resetPasswordUrl = $this->router->generate(
            'app_auth_reset_password',
            ['code' => $user->getResetPasswordCode()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $email = $this->buildEmail($user)
            ->subject('ac: Did you forget your password ?')
            ->textTemplate('email/user/resetPassword.txt.twig')
            ->htmlTemplate('email/user/resetPassword.html.twig')
            ->context([
                'heading' => 'Password reset query',
                'base_url' => $this->getBaseUrl(),
                'user' => $user,
                'resetPasswordUrl' => $resetPasswordUrl,
            ]);
        $this->send($email);
    }
}
