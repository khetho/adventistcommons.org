<?php

namespace App\DataFixtures;

use App\Entity\ContentRevision;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class ContentRevisionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $contentRevisions = [];
        for ($iProduct = 0; $iProduct < 8; $iProduct++) {
            for ($iSection = 0; $iSection < 7; $iSection++) {
                for ($iContent = 0; $iContent < 6; $iContent++) {
                    for ($iProject = 0; $iProject < 3; $iProject++) {
                        $contentRevision = new ContentRevision();
                        $contentRevision->setContent('Previously translated content.');
                        $contentRevision->setContent2($this->getReference(ContentFixtures::buildReferenceName($iProduct, $iSection, $iContent)));
                        $contentRevision->setProject($this->getReference(ProjectFixtures::buildReferenceName($iProduct, $iProject)));
                        $contentRevision->setUser($this->getReference('user-0001'));
                        $date = new \DateTime();
                        $date->sub(new \DateInterval(sprintf('P%dD', rand(0, 300))));
                        $contentRevision->setCreatedAt($date);
                        $manager->persist($contentRevision);
                        $contentRevisions[$iProduct][$iSection][$iContent][$iProject][1] = $contentRevision;

                        $contentRevision = new ContentRevision();
                        $contentRevision->setContent('Translated content.');
                        $contentRevision->setContent2($this->getReference(ContentFixtures::buildReferenceName($iProduct, $iSection, $iContent)));
                        $contentRevision->setProject($this->getReference(ProjectFixtures::buildReferenceName($iProduct, $iProject)));
                        $contentRevision->setUser($this->getReference('user-0001'));
                        $date = new \DateTime();
                        $date->sub(new \DateInterval(sprintf('P%dD', rand(0, 300))));
                        $contentRevision->setCreatedAt($date);
                        $manager->persist($contentRevision);
                        $contentRevisions[$iProduct][$iSection][$iContent][$iProject][2] = $contentRevision;
                    }
                }
            }
        }
        $manager->flush();

        for ($iProduct = 0; $iProduct < 8; $iProduct++) {
            for ($iSection = 0; $iSection < 7; $iSection++) {
                for ($iContent = 0; $iContent < 6; $iContent++) {
                    for ($iProject = 0; $iProject < 3; $iProject++) {
                        $this->addReference(self::buildReferenceName($iProduct, $iSection, $iContent, $iProject, 1), $contentRevisions[$iProduct][$iSection][$iContent][$iProject][1]);
                        $this->addReference(self::buildReferenceName($iProduct, $iSection, $iContent, $iProject, 2), $contentRevisions[$iProduct][$iSection][$iContent][$iProject][2]);
                    }
                }
            }
        }
    }
    
    public static function buildReferenceName($productCode, $sectionCode, $contentCode, $projectCode, $revision)
    {
        return sprintf('revision-%04d-%04d-%04d-%04d-%04d', $productCode, $sectionCode, $contentCode, $projectCode, $revision);
    }
    
    public function getDependencies()
    {
        return [
            ContentFixtures::class,
            ProjectFixtures::class,
        ];
    }
}
