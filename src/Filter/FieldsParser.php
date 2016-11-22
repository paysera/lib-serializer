<?php

namespace Paysera\Component\Serializer\Filter;

class FieldsParser
{

    /**
     * @param array $fields
     * @param array $scope
     *
     * @return FieldsConfig
     */
    public function parseFields(array $fields = null, array $scope = array())
    {
        $fieldsConfig = $this->parseUnscopedFields($fields);
        foreach ($scope as $fieldName) {
            $fieldsConfig = $this->parseUnscopedFields($fieldsConfig->getFieldExtensions($fieldName));
        }
        return $fieldsConfig;
    }

    /**
     * @param array $fields
     *
     * @throws \InvalidArgumentException
     * @return FieldsConfig
     */
    public function parseUnscopedFields(array $fields = null)
    {
        if ($fields === null) {
            return $this->createWithDefaultsIncluded();
        }

        $defaultsIncluded = false;
        $includedFields = array();
        $fieldExtensions = array();

        foreach ($fields as $fieldDefinition) {
            // todo: take curly braces? see commented test-case for possible usage
            foreach (explode(',', $fieldDefinition) as $field) {
                $list = explode('.', $field, 2);
                if (isset($list[1]) && $list[1] === '') {
                    throw new \InvalidArgumentException('Invalid field provided, field cannot end with a dot');
                }
                $name = $list[0];
                $extension = isset($list[1]) ? $list[1] : null;

                if ($name === '*') {
                    $defaultsIncluded = true;
                } else {
                    $includedFields[] = $name;
                    $fieldExtensions[$name][] = $extension !== null ? $extension : '*';
                }
            }
        }

        return new FieldsConfig($defaultsIncluded, $includedFields, $fieldExtensions);
    }

    /**
     * @return FieldsConfig
     */
    protected function createWithDefaultsIncluded()
    {
        return new FieldsConfig(true, array(), array());
    }
} 
