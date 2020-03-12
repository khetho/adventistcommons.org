<?php

namespace App\Tool;

use App\Tool\FineDiff\FineDiff;

class Differ
{
    public function combine(
        $old,
        $new
    ): string {
        $diff = new FineDiff($old, $new, FineDiff::$characterGranularity);

        return $diff->renderDiffToHTML();
    }
}
