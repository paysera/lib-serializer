<?php

namespace Paysera\Component\Serializer\Entity;

interface NormalizationContextInterface
{

    /**
     * Gets fields
     *
     * @return array
     */
    public function getFields();

    /**
     * @param string $fieldName
     *
     * @return NormalizationContextInterface
     */
    public function createScopedContext($fieldName);

    /**
     * @return array
     */
    public function getScope();

}
