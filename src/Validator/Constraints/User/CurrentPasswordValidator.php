<?php

namespace App\Validator\Constraints\User;

use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class CurrentPasswordValidator extends ConstraintValidator
{
    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    /**
     * @var Security
     */
    private $security;

    /**
     * @param EncoderFactoryInterface  $encoderFactory
     * @param Security $security
     */
    public function __construct(EncoderFactoryInterface  $encoderFactory, Security $security)
    {
        $this->encoderFactory  = $encoderFactory;
        $this->security = $security;
    }

    /**
     * @param string     $currentPassword
     * @param Constraint $constraint
     */
    public function validate($currentPassword, Constraint $constraint)
    {
        if (!$constraint instanceof CurrentPassword) {
            throw new UnexpectedTypeException($constraint, CurrentPassword::class);
        }

        if (!is_string($currentPassword)) {
            throw new UnexpectedValueException($currentPassword, 'string');
        }

        $currentUser = $this->security->getUser();
        $encoder = $this->encoderFactory->getEncoder($currentUser);
        $isValid = $encoder->isPasswordValid($currentUser->getPassword(), $currentPassword, null);

        if (!$isValid) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
