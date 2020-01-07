<?php

namespace App\DataFixtures;

use App\Entity\Project;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class ProjectFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $projects = [];
        $languages = LanguageFixtures::getData();
        $languageMax = count($languages) - 1;
        for ($iProduct = 0; $iProduct < 10; $iProduct++) {
            for ($iProject = 0; $iProject < 3; $iProject++) {
                $project = new Project();
                $languageCode = $languages[rand(0, $languageMax)][1];
                $project->setLanguage($this->getReference(LanguageFixtures::buildReferenceName($languageCode)));
                $project->setProduct($this->getReference(ProductFixtures::buildReferenceName($iProduct)));
                $manager->persist($project);
                $projects[$iProduct][$iProject] = $project;
            }
        }
        $manager->flush();
        
        for ($iProduct = 0; $iProduct < 10; $iProduct++) {
            for ($iProject = 0; $iProject < 3; $iProject++) {
                $this->addReference(self::buildReferenceName($iProduct, $iProject), $projects[$iProduct][$iProject]);
            }
        }
    }
    
    public static function buildReferenceName($productCode, $projectCode)
    {
        return sprintf('project-%04d-%04d', $productCode, $projectCode);
    }
    
    public function getDependencies()
    {
        return [
            LanguageFixtures::class,
            ProductFixtures::class,
        ];
    }
}
