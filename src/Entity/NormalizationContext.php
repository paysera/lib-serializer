<?php

namespace Paysera\Component\Serializer\Entity;

class NormalizationContext implements NormalizationContextInterface
{

    /**
     * @var array of string
     */
    protected $fields = array();

    /**
     * @var array
     */
    protected $scope = array();

    /**
     * Sets fields
     *
     * @param array $fields
     *
     * @return $this
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * Gets fields
     *
     * @return array
     */
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

    /**
     * @return array
     */
    public function getScope()
    {
        return $this->scope;
    }


}
