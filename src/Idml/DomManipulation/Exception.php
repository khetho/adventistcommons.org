<?php

namespace AdventistCommons\Idml\DomManipulation;

use AdventistCommons\Idml\Entity\Story;

class Exception extends \Exception
{
    public function setStory(Story $story)
    {
        $this->message = sprintf("Story %s :\n%s", $story->getKey(), $this->message);
    }
}
