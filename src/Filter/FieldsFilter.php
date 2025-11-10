<?php

namespace Paysera\Component\Serializer\Filter;

class FieldsFilter
{
    /**
     * @var FieldsParser
     */
    protected $fieldsParser;

    public function __construct(FieldsParser $fieldsParser)
    {
        $this->fieldsParser = $fieldsParser;
    }


    /**
     * @param array $data
     * @param null|array $fields
     * @param array $scope
     *
     * @return array
     */
    public function filter($data, ?array $fields = null, array $scope = [])
    {
        $fieldsConfig = $this->fieldsParser->parseFields($fields, $scope);

        if ($fieldsConfig->areDefaultsIncluded()) {
            return $data;
        }

        if ($this->isAssociativeArray($data)) {
            $result = $this->filterByConfig($data, $fieldsConfig);
            if (is_array($result) && count($result) === 0) {
                $result = new \ArrayObject();
            }
            return $result;
        } else {
            foreach ($data as &$item) {
                $item = is_array($item) ? $this->filterByConfig($item, $fieldsConfig) : $item;
            }
            return $data;
        }
    }

    /**
     * @param array        $data
     * @param FieldsConfig $fieldsConfig
     *
     * @return array
     */
    protected function filterByConfig($data, FieldsConfig $fieldsConfig)
    {
        $result = array();
        foreach ($data as $fieldName => $value) {
            if ($fieldsConfig->isIncluded($fieldName)) {
                if (is_array($value)) {
                    $value = $this->filter($value, $fieldsConfig->getFieldExtensions($fieldName));
                }
                $result[$fieldName] = $value;
            }
        }

        return $result;
    }

    /**
     * @param array $array
     *
     * @return bool
     */
    protected function isAssociativeArray($array)
    {
        for ($i = 0; $i < count($array); $i++) {
            if (!isset($array[$i])) {
                return true;
            }
        }
        return false;
    }
}
