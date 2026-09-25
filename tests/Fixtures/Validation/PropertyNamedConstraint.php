<?php

namespace Paysera\Component\Serializer\Tests\Fixtures\Validation;

use Symfony\Component\Validator\Constraint;

class PropertyNamedConstraint extends Constraint
{
    public const FAILURE_ERROR = '7a2b3c4d-2222-4a5b-8c9d-0e1f2a3b4c5d';

    protected static $errorNames = [self::FAILURE_ERROR => 'FAILURE_ERROR'];

    public $message = 'Property named failure.';

    public $failureCode = self::FAILURE_ERROR;

    public function validatedBy(): string
    {
        return FailingConstraintValidator::class;
    }
}
