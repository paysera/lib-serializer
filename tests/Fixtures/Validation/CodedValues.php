<?php

namespace Paysera\Component\Serializer\Tests\Fixtures\Validation;

use Symfony\Component\Validator\Mapping\ClassMetadata;

class CodedValues
{
    private $dualNamed;
    private $constantNamed;
    private $propertyNamed;
    private $unnamed;
    private $uncoded;

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $uncoded = new UnnamedConstraint();
        $uncoded->failureCode = null;

        $metadata->addPropertyConstraint('dualNamed', new DualNamedConstraint());
        $metadata->addPropertyConstraint('constantNamed', new ConstantNamedConstraint());
        $metadata->addPropertyConstraint('propertyNamed', new PropertyNamedConstraint());
        $metadata->addPropertyConstraint('unnamed', new UnnamedConstraint());
        $metadata->addPropertyConstraint('uncoded', $uncoded);
    }
}
