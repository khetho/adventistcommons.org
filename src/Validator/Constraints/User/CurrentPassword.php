<?php

namespace App\Validator\Constraints\User;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CurrentPassword extends Constraint
{
    public $message = "Your current password is not valid";
}
