<?php

namespace App\DataFixtures;

use App\Entity\Audience;
use App\Entity\Series;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class SerieFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        foreach ([
            'Sample Serie',
        ] as $name) {
            $serie = new Series();
            $serie->setName($name);
            $manager->persist($serie);
        }
    }
}
