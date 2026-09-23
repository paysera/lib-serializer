<?php

namespace Paysera\Component\Serializer\Tests\Fixtures\Validation;

use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * An entity validated through Symfony's metadata, the way applications validate request entities: built-in constraints
 * on camelCase properties and a nested object under Valid.
 * (Constraints are built without options so the same fixture runs on every Symfony line the library allows.)
 */
class Account
{
    private $accountNumber;
    private $nickname;
    private $beneficiary;

    public function __construct($accountNumber, $nickname, Beneficiary $beneficiary)
    {
        $this->accountNumber = $accountNumber;
        $this->nickname = $nickname;
        $this->beneficiary = $beneficiary;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('accountNumber', new NotBlank());
        $metadata->addPropertyConstraint('nickname', new Blank());
        $metadata->addPropertyConstraint('beneficiary', new Valid());
    }
}
