<?php

namespace Paysera\Component\Serializer\Tests\Fixtures\Validation;

use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * One property per way a custom constraint can report its error code; every constraint here always fails.
 */
class CodedValues
{
    private $dualNamed;
    private $propertyNamed;
    private $unnamed;
    private $uncoded;

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $uncoded = new UnnamedConstraint();
        $uncoded->failureCode = null;

        $metadata->addPropertyConstraint('dualNamed', new DualNamedConstraint());
        $metadata->addPropertyConstraint('propertyNamed', new PropertyNamedConstraint());
        $metadata->addPropertyConstraint('unnamed', new UnnamedConstraint());
        $metadata->addPropertyConstraint('uncoded', $uncoded);
    }
}
