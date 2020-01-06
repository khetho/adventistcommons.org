<?php

namespace App\DataFixtures;

use App\Entity\Content;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class ContentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $contents = [];
        for ($iProduct = 0; $iProduct < 10; $iProduct++) {
            for ($iSection = 0; $iSection < 10; $iSection++) {
                for ($iContent = 0; $iContent < 10; $iContent++) {
                    $content = new Content();
                    $content->setSection($this->getReference(SectionFixtures::buildReferenceName($iProduct, $iSection)));
                    $content->setContent('Test line to translate.');
                    $manager->persist($content);
                    $contents[$iProduct][$iSection][$iContent] = $content;
                }
            }
        }
        $manager->flush();
        
        for ($iProduct = 0; $iProduct < 10; $iProduct++) {
            for ($iSection = 0; $iSection < 10; $iSection++) {
                for ($iContent = 0; $iContent < 10; $iContent++) {
                    $this->addReference(self::buildReferenceName($iProduct, $iSection, $iContent), $contents[$iProduct][$iSection][$iContent]);
                }
            }
        }
    }
    
    public static function buildReferenceName($productCode, $sectionCode, $contentCode)
    {
        return sprintf('content-%04d-%04d-%04d', $productCode, $sectionCode, $contentCode);
    }
    
    public function getDependencies()
    {
        return [
            SectionFixtures::class,
        ];
    }
}
