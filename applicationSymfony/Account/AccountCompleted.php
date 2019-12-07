<?php

namespace App\Account;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;

class AccountCompleted implements EventSubscriberInterface
{
    private $security;
    private $router;
    
    public function __construct(Security $security, RouterInterface $router)
    {
        $this->security = $security;
        $this->router = $router;
    }
    
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'checkAccount',
        ];
    }

    public function checkAccount(RequestEvent $event)
    {
        $user = $this->security->getUser();
            
        $currentRoute = $event->getRequest()->attributes->get('_route');
        if ($user && !$user->getMotherLanguage() && !in_array($currentRoute, [null, 'app_account_complete'])) {
            $event->getRequest()->getSession()->getFlashBag()->add('warning', 'Please complete your account before you do.');
            $response = new RedirectResponse($this->router->generate('app_account_complete'));
            $event->setResponse($response);
        }
    }
}
