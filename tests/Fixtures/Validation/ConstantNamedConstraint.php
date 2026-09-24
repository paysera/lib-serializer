<?php

namespace Paysera\Component\Serializer\Tests\Fixtures\Validation;

use Symfony\Component\Validator\Constraint;

/**
 * Names its error only through the `ERROR_NAMES` constant, which Symfony reads since 6.1; up to 6.0 it reads only the
 * `$errorNames` property.
 */
class ConstantNamedConstraint extends Constraint
{
    public const FAILURE_ERROR = '9c4d5e6f-4444-4a5b-8c9d-0e1f2a3b4c5d';

    protected const ERROR_NAMES = [self::FAILURE_ERROR => 'FAILURE_ERROR'];

    public $message = 'Constant named failure.';

    public $failureCode = self::FAILURE_ERROR;

    public function validatedBy(): string
    {
        return FailingConstraintValidator::class;
    }
}
