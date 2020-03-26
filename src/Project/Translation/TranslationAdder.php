<?php

namespace App\Project\Translation;

use App\Entity\ContentRevision;
use App\Entity\Project;
use App\Entity\Sentence;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

class TranslationAdder
{
    private $registry;
    private $user;

    public function __construct(ManagerRegistry $registry, Security $security)
    {
        $this->registry = $registry;
        $this->user = $security->getUser();
    }

    public function addTranslation(Sentence $sentence, Project $project, string $content): ?ContentRevision
    {
        $repo = $this->registry->getRepository(ContentRevision::class);
        $previous = $repo->findLatestForSentenceAndProject($sentence, $project);
        if ($previous && $previous->getContent() === $content) {
            return null;
        }
        $revision = new ContentRevision();
        $revision->setTranslator($this->user);
        $revision->setSentence($sentence);
        $revision->setProject($project);
        $revision->setContent($content);

        $manager = $this->registry->getManager();

        $manager->persist($revision);
        $manager->flush();

        return $revision;
    }
}
