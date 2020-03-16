<?php

namespace App\DataFixtures;

use App\Entity\Paragraph;
use App\Entity\Sentence;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class SentenceFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $contents = [];
        for ($iProduct = 0; $iProduct < 10; $iProduct++) {
            for ($iSection = 0; $iSection < 10; $iSection++) {
                for ($iParagraph = 0; $iParagraph < 10; $iParagraph++) {
                    for ($iSentence = 0; $iSentence < 3; $iSentence++) {
                        $content = new Sentence();
                        $content->setParagraph($this->getReference(ParagraphFixtures::buildReferenceName($iProduct, $iSection, $iParagraph)));
                        $content->setContent('Test line to translate.');
                        $manager->persist($content);
                        $contents[$iProduct][$iSection][$iParagraph] = $content;
                    }
                }
            }
        }
        $manager->flush();
        
        for ($iProduct = 0; $iProduct < 10; $iProduct++) {
            for ($iSection = 0; $iSection < 10; $iSection++) {
                for ($iParagraph = 0; $iParagraph < 10; $iParagraph++) {
                    for ($iSentence = 0; $iSentence < 3; $iSentence++) {
                        $this->addReference(self::buildReferenceName($iProduct, $iSection, $iParagraph, $iSentence), $contents[$iProduct][$iSection][$iParagraph]);
                    }
                }
            }
        }
    }
    
    public static function buildReferenceName($productCode, $sectionCode, $paragraphCode, $sentenceCode)
    {
        return sprintf('sentence-%04d-%04d-%04d-%04d', $productCode, $sectionCode, $paragraphCode, $sentenceCode);
    }
    
    public function getDependencies()
    {
        return [
            ParagraphFixtures::class,
        ];
    }
}
