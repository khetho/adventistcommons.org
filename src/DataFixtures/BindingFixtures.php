<?php

namespace App\DataFixtures;

use App\Entity\Binding;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class BindingFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        foreach ([
            'Hardcover',
            'Perfect Bound',
            'Spiral Bound',
            'Saddle Stitch',
            'Folded',
        ] as $name) {
            $binding = new Binding();
            $binding->setName($name);
            $manager->persist($binding);
        }
        $manager->flush();
    }
}
