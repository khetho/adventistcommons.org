<?php

namespace App\ContentRevision;

use App\Tool\Differ;

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
