<?php

namespace App\DataFixtures;

use App\Entity\Paragraph;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class ParagraphFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $contents = [];
        for ($iProduct = 0; $iProduct < 10; $iProduct++) {
            for ($iSection = 0; $iSection < 10; $iSection++) {
                for ($iParagraph = 0; $iParagraph < 10; $iParagraph++) {
                    $content = new Paragraph();
                    $content->setSection($this->getReference(SectionFixtures::buildReferenceName($iProduct, $iSection)));
                    $manager->persist($content);
                    $contents[$iProduct][$iSection][$iParagraph] = $content;
                }
            }
        }
        $manager->flush();
        
        for ($iProduct = 0; $iProduct < 10; $iProduct++) {
            for ($iSection = 0; $iSection < 10; $iSection++) {
                for ($iParagraph = 0; $iParagraph < 10; $iParagraph++) {
                    $this->addReference(self::buildReferenceName($iProduct, $iSection, $iParagraph), $contents[$iProduct][$iSection][$iParagraph]);
                }
            }
        }
    }
    
    public static function buildReferenceName($productCode, $sectionCode, $paragraphCode)
    {
        return sprintf('paragraph-%04d-%04d-%04d', $productCode, $sectionCode, $paragraphCode);
    }
    
    public function getDependencies()
    {
        return [
            SectionFixtures::class,
        ];
    }
}
