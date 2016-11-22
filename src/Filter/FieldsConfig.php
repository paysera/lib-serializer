<?php

namespace Paysera\Component\Serializer\Filter;

class FieldsConfig
{
    /**
     * @var array
     */
    protected $includedFields;

    /**
     * @var array
     */
    protected $fieldExtensions;

    /**
     * @var bool
     */
    protected $defaultsIncluded;

    public function __construct($defaultsIncluded, array $includedFields, array $fieldExtensions)
    {
        $this->fieldExtensions = $fieldExtensions;
        $this->defaultsIncluded = (bool)$defaultsIncluded;
        $this->includedFields = $includedFields;
    }

    /**
     * @param string $fieldName
     * @param bool   $isDefaultField
     *
     * @return bool
     */
    public function isIncluded($fieldName, $isDefaultField = true)
    {
        if ($isDefaultField && $this->defaultsIncluded) {
            return true;
        } else {
            return in_array((string)$fieldName, $this->includedFields, true);
        }
    }

    /**
     * @param string $fieldName
     *
     * @return array
     */
    public function getFieldExtensions($fieldName)
    {
        if (isset($this->fieldExtensions[$fieldName])) {
            $extensions = $this->fieldExtensions[$fieldName];
            if ($this->areDefaultsIncluded()) {
                $extensions[] = '*';
            }
            return $extensions;
        } else {
            return $this->defaultsIncluded ? array('*') : array();
        }
    }

    /**
     * @return bool
     */
    public function areDefaultsIncluded()
    {
        return $this->defaultsIncluded;
    }
} 
