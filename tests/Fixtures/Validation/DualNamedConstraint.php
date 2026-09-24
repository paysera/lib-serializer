<?php

namespace Paysera\Component\Serializer\Tests\Fixtures\Validation;

use Symfony\Component\Validator\Constraint;

/**
 * Names its error both ways: the `ERROR_NAMES` constant Symfony reads since 6.1 (the only one on 7.x) and the
 * `$errorNames` property Symfony reads up to 6.4 (the only one up to 6.0).
 */
class DualNamedConstraint extends Constraint
{
    public const FAILURE_ERROR = '6f1b2c3d-1111-4a5b-8c9d-0e1f2a3b4c5d';

    protected const ERROR_NAMES = [self::FAILURE_ERROR => 'FAILURE_ERROR'];

    protected static $errorNames = [self::FAILURE_ERROR => 'FAILURE_ERROR'];

    public $message = 'Dual named failure.';

    public $failureCode = self::FAILURE_ERROR;

    public function validatedBy(): string
    {
        return FailingConstraintValidator::class;
    }
}
