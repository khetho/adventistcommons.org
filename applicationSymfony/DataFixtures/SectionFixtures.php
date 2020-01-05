<?php

namespace App\DataFixtures;

use App\Entity\Section;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class SectionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $sections = [];
        for ($iProduct = 0; $iProduct < 10; $iProduct++) {
            for ($iSection = 0; $iSection < 10; $iSection++) {
                $section = new Section();
                $section->setName(sprintf('Product %04d Section', $iProduct, $iSection));
                $section->setProduct($this->getReference(ProductFixtures::buildReferenceName($iProduct)));
                $manager->persist($section);
                $sections[$iProduct][$iSection] = $section;
            }
        }
        $manager->flush();
        
        for ($iProduct = 0; $iProduct < 10; $iProduct++) {
            for ($iSection = 0; $iSection < 10; $iSection++) {
                $this->addReference(self::buildReferenceName($iProduct, $iSection), $sections[$iProduct][$iSection]);
            }
        }
    }
    
    public static function buildReferenceName($productCode, $sectionCode)
    {
        return sprintf('content-%04d-%04d', $productCode, $sectionCode);
    }
    
    public function getDependencies()
    {
        return [
            ProductFixtures::class,
        ];
    }
}
