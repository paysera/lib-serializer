<?php

namespace Paysera\Component\Serializer\Entity;

class NormalizationContext implements NormalizationContextInterface
{
    /**
     * @var array
     */
    protected $fields;

    /**
     * @var array
     */
    protected $scope;

    public function __construct()
    {
        $this->fields = [];
        $this->scope = [];
    }

    public function setFields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }

    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param string $fieldName
     *
     * @return NormalizationContextInterface
     */
    public function createScopedContext($fieldName)
    {
        $context = clone $this;
        $context->scope[] = $fieldName;

        return $context;
    }

    public function getScope()
    {
        return $this->scope;
    }
}
