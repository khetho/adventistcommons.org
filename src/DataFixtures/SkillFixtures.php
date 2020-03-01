<?php

namespace App\DataFixtures;

use App\Entity\Skill;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class SkillFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        foreach ([        
            ['Graphic design','GD'],
            ['Web development','WD'],
            ['Software development','SD'],
            ['Writing/editing','WE'],
            ['Video editing','VE'],
            ['Illustration (digital art)','IDA'],
        ] as $data) {
            $skill = new Skill();
            $skill->setName($data[0]);
            $skill->setCode($data[1]);
            $manager->persist($skill);
        }
        $manager->flush();
    }
}
