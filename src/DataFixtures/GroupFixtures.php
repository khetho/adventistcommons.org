<?php

namespace App\DataFixtures;

use App\Entity\Group;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GroupFixtures extends Fixture
{
    public const GROUP_ADMIN = 'admin-group';
    public const GROUP_MEMBER = 'member-group';

    public function load(ObjectManager $manager)
    {
        $memberGroup = new Group();
        $memberGroup->setName('member');
        $memberGroup->setDescription('Member of the app, mostly translators');
        
        $adminGroup = new Group();
        $adminGroup->setName('admin');
        $adminGroup->setDescription('Admin of the app');
        
        $manager->persist($memberGroup);
        $manager->persist($adminGroup);
        $manager->flush();
        
        $this->addReference(self::GROUP_MEMBER, $memberGroup);
        $this->addReference(self::GROUP_ADMIN, $adminGroup);
    }
}
