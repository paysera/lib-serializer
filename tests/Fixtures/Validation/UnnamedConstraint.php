<?php

namespace Paysera\Component\Serializer\Tests\Fixtures\Validation;

use Symfony\Component\Validator\Constraint;

/**
 * Reports a code it gives no name, or no code at all when `$failureCode` is null.
 */
class UnnamedConstraint extends Constraint
{
    const FAILURE_ERROR = '8b3c4d5e-3333-4a5b-8c9d-0e1f2a3b4c5d';

    public $message = 'Unnamed failure.';

    public $failureCode = self::FAILURE_ERROR;

    public function validatedBy(): string
    {
        return FailingConstraintValidator::class;
    }
}
