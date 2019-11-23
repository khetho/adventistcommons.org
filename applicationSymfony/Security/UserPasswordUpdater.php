<?php

namespace App\Security;

use App\Entity\User;
use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserPasswordUpdater implements EventSubscriberInterface
{

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->passwordEncoder = $userPasswordEncoder;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['setPassword', EventPriorities::POST_VALIDATE],
        ];
    }

    public function setPassword(ViewEvent $event)
    {
        $user = $event->getControllerResult();

        if ($user instanceof User && $user->getPlainPassword()) {
            $password = $this->passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
        }
    }
}


// <?php

// namespace App\Security;

// use App\Entity\User;
// use Doctrine\Common\EventSubscriber;
// use Doctrine\ORM\Event\LifecycleEventArgs;
// use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
// use Doctrine\ORM\Events;

// class UserPasswordUpdater implements EventSubscriber
// {
//     private $passwordEncoder;

//     public function __construct(UserPasswordEncoderInterface $passwordEncoder)
//     {
//         $this->passwordEncoder = $passwordEncoder;
//     }

//     public function getSubscribedEvents()
//     {
//         return [
//             Events::prePersist,
//             Events::preUpdate,
//         ];
//     }

//     public function prePersist(LifecycleEventArgs $args): void
//     {
//         $entity = $args->getEntity();

//         if (!$entity instanceof User) {
//             return;
//         }

//         $this->encodePassword($entity);
//     }
    
//     public function preUpdate(LifecycleEventArgs $args): void
//     {
//         $entity = $args->getEntity();
//         dump($entity);die;

//         if (!$entity instanceof User) {
//             return;
//         }

//         $this->encodePassword($entity);
//     }
    
//     private function encodePassword(User $entity)
//     {
//         $plainPassword = $entity->getPlainPassword();
//         dump($plainPassword);
//         die('ok');
//         if (!$plainPassword) {
//             return;
//         }
//         $encoded = $this->passwordEncoder->encodePassword($entity, $plainPassword);
//         $entity->setPassword($encoded);
//     }
// }
