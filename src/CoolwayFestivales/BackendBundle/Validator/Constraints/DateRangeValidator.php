<?php

namespace CoolwayFestivales\BackendBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DateRangeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if ($value == 'test') {
            $this->context->addViolation($constraint->message, array('%string%' => $value));
        }
    }
}