<?php

namespace Paysera\Component\Serializer\Tests\Fixtures\Validation;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * Its only constraint belongs to the `Strict` group, so it fails only when that group is validated.
 */
class GroupedAccount
{
    private $accountNumber;

    public function __construct($accountNumber)
    {
        $this->accountNumber = $accountNumber;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $strict = new NotBlank();
        $strict->groups = ['Strict'];
        $metadata->addPropertyConstraint('accountNumber', $strict);
    }
}
