<?php

namespace Paysera\Component\Serializer\Tests\Fixtures\Validation;

use Symfony\Component\Validator\Constraint;

/**
 * Names its error only through the `$errorNames` property, which Symfony reads up to 6.4 and no longer reads on 7.x.
 */
class PropertyNamedConstraint extends Constraint
{
    const FAILURE_ERROR = '7a2b3c4d-2222-4a5b-8c9d-0e1f2a3b4c5d';

    protected static $errorNames = [self::FAILURE_ERROR => 'FAILURE_ERROR'];

    public $message = 'Property named failure.';

    public $failureCode = self::FAILURE_ERROR;

    public function validatedBy(): string
    {
        return FailingConstraintValidator::class;
    }
}
