<?php

namespace App\Tool\FineDiff;

class FineDiffInsertOp extends FineDiffOp
{
    private $text;

    public function __construct($text)
    {
        $this->text = $text;
    }
    public function getFromLen()
    {
        return 0;
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
        $toLen = strlen($this->text);
        if ($toLen === 1) {
            return "i:{$this->text}";
        }
        return "i{$toLen}:{$this->text}";
    }
}
