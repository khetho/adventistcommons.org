<?php

namespace App\Product\EventSubscriber;

use AdventistCommons\Basics\StringFunctions;
use App\Entity\Product;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class SlugSetter implements EventSubscriber
{
    private $stringFunctions;

    public function __construct(StringFunctions $stringFunctions)
    {
        $this->stringFunctions = $stringFunctions;
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
        $this->setSlug($entity = $args->getEntity());
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $this->setSlug($entity = $args->getEntity());
    }

    private function setSlug($entity)
    {
        if (!$entity instanceof Product) {
            return;
        }

        $entity->setSlug(
            $this->stringFunctions->slugify(
                $entity->getSlug() ?? $entity->setName()
            )
        );
    }
}
