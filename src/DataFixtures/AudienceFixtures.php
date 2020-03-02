<?php

namespace App\DataFixtures;

use App\Entity\Audience;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class AudienceFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        foreach ([
            'Christian',
            'Muslim',
            'Buddhist',
            'Hindu',
            'Sikh',
            'Animist',
            'Secular',
        ] as $name) {
            $audience = new Audience();
            $audience->setName($name);
            $manager->persist($audience);
        }
        $manager->flush();
    }
}
