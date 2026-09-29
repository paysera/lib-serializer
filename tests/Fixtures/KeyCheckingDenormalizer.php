<?php

namespace Paysera\Component\Serializer\Tests\Fixtures;

use Paysera\Component\Serializer\Normalizer\BaseDenormalizer;

class KeyCheckingDenormalizer extends BaseDenormalizer
{
    public function mapToEntity($data)
    {
        return $data;
    }

    public function checkAvailableKeys($data, $availableKeys)
    {
        parent::checkAvailableKeys($data, $availableKeys);
    }

    public function checkRequiredKeys($data, $requiredKeys)
    {
        parent::checkRequiredKeys($data, $requiredKeys);
    }

    public function checkOnlyOneKeyExists($data, $keys)
    {
        parent::checkOnlyOneKeyExists($data, $keys);
    }
}
