<?php

namespace AdventistCommons\Basics;

class Clock
{
    private $now;
    
    public function __construct()
    {
        $this->now = new \DateTime();
    }
    
    /**
     * @return \DateTime
     */
    public function now(): \DateTime
    {
        return $this->now;
    }
}
