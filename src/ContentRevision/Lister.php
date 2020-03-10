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
        $revisions = (array) $revisions;
        $previousContent = '';

        foreach (array_reverse($revisions) as &$revision) {
            $revision->setContent(
                $this->differ->combine(
                    $previousContent,
                    $revision->getContent()
                )
            );
        }

        return array_reverse($revisions);
    }
}
