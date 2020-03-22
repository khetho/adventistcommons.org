<?php

namespace App\Definition\EventSubscriber;

use App\Definition\Publisher;
use App\Entity\Definition;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class PublishDefinitions implements EventSubscriber
{
    private $publisher;

    public function __construct(Publisher $publisher)
    {
        $this->publisher = $publisher;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->publish($args->getEntity());
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $this->publish($args->getEntity());
    }

    private function publish($entity)
    {
        if (!$entity instanceof Definition) {
            return;
        }

        $this->publisher->publishAll();
    }
}
