<?php

namespace Paysera\Component\Serializer\Tests\Fixtures;

use Paysera\Component\Serializer\Accessor\FieldAccessorInterface;

/**
 * Reads and writes one public property, so the distributed-field tests do not
 * depend on how Symfony's PropertyAccess component behaves on each version.
 */
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
