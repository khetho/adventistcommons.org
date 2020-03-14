<?php

namespace App\Tool\FineDiff;

class FineDiffReplaceOp extends FineDiffOp
{
    private $fromLen;
    private $text;

    public function __construct($fromLen, $text)
    {
        $this->fromLen = $fromLen;
        $this->text = $text;
    }
    public function getFromLen()
    {
        return $this->fromLen;
    }
    public function getToLen()
    {
        return strlen($this->text);
    }
    public function getText()
    {
        return $this->text;
    }
    public function getOpcode()
    {
        $delOpcode = 'd';
        $delOpcode .= $this->fromLen === 1 ? '' : "{$this->fromLen}";
        $toLen = strlen($this->text);
        if ($toLen === 1) {
            return "{$delOpcode}i:{$this->text}";
        }
        return "{$delOpcode}i{$toLen}:{$this->text}";
    }
}
