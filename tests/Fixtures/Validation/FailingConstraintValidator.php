<?php

namespace Paysera\Component\Serializer\Tests\Fixtures\Validation;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class FailingConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        $builder = $this->context->buildViolation($constraint->message);
        if ($constraint->failureCode !== null) {
            $builder->setCode($constraint->failureCode);
        }
        $builder->addViolation();
    }
}
