<?php

namespace AdventistCommons\Idml\Entity;

class Content
{
    private $key;
    private $content;
    private $section;
    
    public function __construct($key, \DOMElement $content, Section $section)
    {
        $this->key = $key;
        $this->content = $content;
        $this->section = $section;
    }
    
    public function getKey(): string
    {
        return $this->key;
    }
    
    public function getText(): string
    {
        return $this->content->nodeValue;
    }
}
