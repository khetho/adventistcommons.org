<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Group;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $user = new User('admin@example.com');
        $user->setActive(true);
        $user->setPlainPassword('pass');
        $user->addGroup($this->getReference(GroupFixtures::GROUP_ADMIN));
        $user->setMotherLanguage($this->getReference(LanguageFixtures::buildReferenceName('zul')));

        $manager->persist($user);
        $manager->flush();
    }
    
    public function getDependencies()
    {
        return [
            GroupFixtures::class,
        ];
    }
}
