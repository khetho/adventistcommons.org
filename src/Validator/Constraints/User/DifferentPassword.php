<?php

namespace App\Validator\Constraints\User;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class DifferentPassword extends Constraint
{
    public $message = "Your cannot change your password to the same";
    
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
