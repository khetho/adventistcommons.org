<?php

namespace App\DataFixtures;

use App\Entity\Skill;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class SkillsFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        foreach ([
            'GD' => 'Graphic design',
            'WD' => 'Web development',
            'SD' => 'Software development',
            'WE' => 'Writing/editing',
            'VE' => 'Video editing',
            'IDA' => 'Illustration (digital art)',
        ] as $code => $name) {
            $skill = new Skill();
            $skill->setCode($code);
            $skill->setName($name);
            $manager->persist($skill);
        }
    }
}
