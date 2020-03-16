<?php

namespace App\ContentRevision;

use App\Entity\Attachment;
use App\Entity\DownloadLog;
use App\Entity\Product;
use App\Entity\Project;
use App\Tool\Differ;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class Lister
{
    private $differ;

    public function __construct(
        Differ $differ
    ) {
        $this->differ = $differ;
    }

    public function diff(array $revisions)
    {
        $revisions = array_reverse($revisions);
        $previousContent = '';
        $revision = null;

        foreach ($revisions as &$revision) {
            $newContent = $this->differ->combine(
                $previousContent,
                $revision->getContent()
            );
            $previousContent = $revision->getContent();
            $revision->setContent(
                $newContent
            );
        }

        return array_reverse($revisions);
    }
}