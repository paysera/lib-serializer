<?php

namespace Paysera\Component\Serializer\Accessor;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

class PropertyPathFieldAccessor implements FieldAccessorInterface
{
    protected $propertyAccessor;
    protected $propertyPath;

    /**
     * @param PropertyAccessorInterface    $propertyAccessor
     * @param PropertyPathInterface|string $propertyPath
     */
    public function __construct(PropertyAccessorInterface $propertyAccessor, $propertyPath)
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->propertyPath = $propertyPath;
    }

    public function getValue($entity)
    {
        return $this->propertyAccessor->getValue($entity, $this->propertyPath);
    }

    public function setValue($entity, $value)
    {
        $this->propertyAccessor->setValue($entity, $this->propertyPath, $value);
    }
} 
