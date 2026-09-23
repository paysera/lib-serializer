<?php

namespace Paysera\Component\Serializer\Tests\Fixtures;

use Paysera\Component\Serializer\Normalizer\BaseDenormalizer;

/**
 * BaseDenormalizer offers its key checks to subclasses only, and none of the
 * library's own denormalizers calls them. Widens them to public so each check
 * can be exercised on its own.
 */
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
