<?php

namespace AdventistCommons\Basics;

use \DateTime;

class Clock
{
    private $now;
    
    public function __construct()
    {
        $this->now = new DateTime();
    }
    
    /**
     * @return DateTime
     */
    public function now(): DateTime
    {
        return $this->now;
    }
}
