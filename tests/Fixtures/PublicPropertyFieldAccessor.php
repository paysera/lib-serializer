<?php

namespace Paysera\Component\Serializer\Tests\Fixtures;

use Paysera\Component\Serializer\Accessor\FieldAccessorInterface;

class PublicPropertyFieldAccessor implements FieldAccessorInterface
{
    /**
     * @var string
     */
    private $property;

    public function __construct($property)
    {
        $this->property = $property;
    }

    public function getValue($entity)
    {
        return isset($entity->{$this->property}) ? $entity->{$this->property} : null;
    }

    public function setValue($entity, $value)
    {
        $entity->{$this->property} = $value;
    }
}
