<?php

namespace App\Project\Translation;

use App\Entity\ContentRevision;
use App\Entity\Project;
use App\Entity\Sentence;
use App\Project\StatusChanger;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

class TranslationAdder
{
    private $registry;
    private $user;
    private $statusChanger;

    public function __construct(ManagerRegistry $registry, Security $security, StatusChanger $statusChanger)
    {
        $this->registry = $registry;
        $this->user = $security->getUser();
        $this->statusChanger = $statusChanger;
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

        $this->statusChanger->startIfUndone($project);
        $this->statusChanger->changeToApprovedIfAllContentApproved($project);

        return $revision;
    }
}
