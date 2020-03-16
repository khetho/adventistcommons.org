<?php

namespace App\Tool;

use PhpTextDiff\FineDiff;

class Differ
{
    public function combine(
        $old,
        $new
    ): string {
        $diff = new FineDiff($old, $new);

        return $diff->renderDiffToHTML();
    }
}
