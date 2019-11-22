<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class CodeIgniterAuthenticationSynchronisation implements EventSubscriberInterface
{
    private $request;
    private $token;
    private $tokenStorage;
    private $eventDispatcher;

    public function __construct(
        RequestStack $requestStack,
        TokenStorageInterface $tokenManager,
        TokenStorageInterface $tokenStorage,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->request = $requestStack->getCurrentRequest();
        $this->token = $tokenManager->getToken();
        $this->tokenStorage = $tokenStorage;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return ['kernel.request' => 'checkCodeIgniterLogin'];
    }

    /**
     * Check if user is connected in code igniter, and connect him in symfony too
     */
    public function checkCodeIgniterLogin()
    {
        $cookies = $this->request->cookies;
        if (!$cookies->has('ci_session')) {
            return;
        }
        $codeIgniterSessionId = $cookies->get('ci_session');
        if (!($userConnectedInCodeIgniter = $this->getUserFromCodeIgniterSessionId($codeIgniterSessionId))) {
            if ($this->token && !($this->token->getUser() != $userConnectedInCodeIgniter)) {
                /**
                 * @todo treate the case : user connceted on ci an sf is not the same
                 */
                throw new \Exception('Error user logged');
            }
            return null;
        }

        $token = new UsernamePasswordToken(
            $userConnectedInCodeIgniter,
            $userConnectedInCodeIgniter->getPassword(),
            'public',
            $userConnectedInCodeIgniter->getRoles()
        );
        $this->tokenStorage->setToken($token);

        $event = new InteractiveLoginEvent($this->request, $token);
        $this->eventDispatcher->dispatch($event);
    }

    private function getUserFromCodeIgniterSessionId($codeIgniterSessionId): ?User
    {
        return null;
    }
}
