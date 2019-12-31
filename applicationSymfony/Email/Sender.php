<?php

namespace App\Email;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\RouterInterface;

class Sender
{
    private $mailer;
    protected $router;
    
    public function __construct(RouterInterface $router, MailerInterface $mailer)
    {
        $this->mailer = $mailer;
        $this->router = $router;
    }

    public function getBaseUrl(): string
    {
        $domainContext = $this->router->getContext();

        return sprintf(
            '%s://%s:%d',
            $domainContext->getScheme(),
            $domainContext->getHost(),
            $domainContext->getScheme() === 'https'
                ? $this->router->getContext()->qetHttpsPort()
                : $this->router->getContext()->getHttpPort()
        );
    }

    public function send(Email $email)
    {
        $this->mailer->send($email);
    }
}
