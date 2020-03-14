<?php

namespace App\Tool\FineDiff;

/**
 * FineDiff ops
 *
 * Collection of ops
 */
class FineDiffOps
{
    public $edits = array();

    public function appendOpcode($opcode, $from, $fromOffset, $fromLen): void
    {
        if ($opcode === 'c') {
            $this->edits[] = new FineDiffCopyOp($fromLen);
            return;
        } elseif ($opcode === 'd') {
            $this->edits[] = new FineDiffDeleteOp($fromLen);
            return;
        }

        /* $opcode === 'i' */
        $this->edits[] = new FineDiffInsertOp(substr($from, $fromOffset, $fromLen));
        return;
    }
}
