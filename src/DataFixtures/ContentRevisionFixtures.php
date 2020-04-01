<?php

namespace App\DataFixtures;

use App\Entity\ContentRevision;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 */
class ContentRevisionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $contentRevisions = [];
        for ($iProduct = 0; $iProduct < 10; $iProduct++) {
            for ($iSection = 0; $iSection < 10; $iSection++) {
                for ($iParagraph = 0; $iParagraph < 10; $iParagraph++) {
                    for ($iSentence = 0; $iSentence < 2; $iSentence++) {
                        for ($iProject = 0; $iProject < 2; $iProject++) {
                            foreach (['Translated content.','Previously translated content.'] as $key => $content) {
                                $contentRevision = new ContentRevision();
                                $contentRevision->setContent($content);
                                $contentRevision->setSentence(
                                    $this->getReference(
                                        SentenceFixtures::buildReferenceName(
                                            $iProduct,
                                            $iSection,
                                            $iParagraph,
                                            $iSentence
                                        )
                                    )
                                );
                                $contentRevision->setProject(
                                    $this->getReference(
                                        ProjectFixtures::buildReferenceName($iProduct, $iProject)
                                    )
                                );
                                $contentRevision->setTranslator($this->getReference('user-0001'));
                                $date = new \DateTime();
                                $date->sub(new \DateInterval(sprintf('P%dD', rand($key*100, ($key*100)+99))));
                                $contentRevision->setCreatedAt($date);
                                $manager->persist($contentRevision);
                                $contentRevisions[$iProduct][$iSection][$iParagraph][$iSentence][$iProject][$key] = $contentRevision;
                            }
                        }
                    }
                }
            }
        }
        $manager->flush();

        for ($iProduct = 0; $iProduct < 10; $iProduct++) {
            for ($iSection = 0; $iSection < 10; $iSection++) {
                for ($iParagraph = 0; $iParagraph < 10; $iParagraph++) {
                    for ($iSentence = 0; $iSentence < 2; $iSentence++) {
                        for ($iProject = 0; $iProject < 2; $iProject++) {
                            foreach (['Translated content.','Previously translated content.'] as $key => $content) {
                                $this->addReference(
                                    self::buildReferenceName(
                                        $iProduct,
                                        $iSection,
                                        $iParagraph,
                                        $iSentence,
                                        $iProject,
                                        $key
                                    ),
                                    $contentRevisions[$iProduct][$iSection][$iParagraph][$iSentence][$iProject][$key]
                                );
                            }
                        }
                    }
                }
            }
        }
    }
    
    public static function buildReferenceName(
        $productCode,
        $sectionCode,
        $paragraphCode,
        $sentenceCode,
        $projectCode,
        $revision
    ) {
        return sprintf(
            'revision-%04d-%04d-%04d-%04d-%04d-%04d',
            $productCode,
            $sectionCode,
            $paragraphCode,
            $sentenceCode,
            $projectCode,
            $revision
        );
    }
    
    public function getDependencies()
    {
        return [
            SentenceFixtures::class,
            ProjectFixtures::class,
        ];
    }
}
