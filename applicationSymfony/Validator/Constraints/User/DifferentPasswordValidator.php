<?php

namespace App\Validator\Constraints\User;

use App\Form\Model\Password;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class DifferentPasswordValidator extends ConstraintValidator
{

    /**
     * @param Password   $password
     * @param Constraint $constraint
     */
    public function validate($password, Constraint $constraint)
    {
        if (!$constraint instanceof DifferentPassword) {
            throw new UnexpectedTypeException($constraint, DifferentPassword::class);
        }

        if (!$password instanceof Password) {
            throw new UnexpectedValueException($password, Password::class);
        }

        if ($password->getNewPassword() === $password->getPreviousPassword()) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
