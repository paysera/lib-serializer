<?php

namespace Paysera\Component\Serializer\Normalizer;

use Paysera\Component\Serializer\Exception\InvalidDataException;

abstract class BaseDenormalizer implements DenormalizerInterface
{
    protected $availableKeysCheckIgnored = false;

    /**
     * Ignores available keys check
     */
    public function ignoreAvailableKeysCheck()
    {
        $this->availableKeysCheckIgnored = true;
    }

    /**
     * Checks for non-available keys. If any of such found, throws exception
     *
     * @param array $data             original data
     * @param array $availableKeys    list of supported keys
     *
     * @throws InvalidDataException
     */
    protected function checkAvailableKeys($data, $availableKeys)
    {
        if (!is_array($data)) {
            throw new InvalidDataException('Content is expected to be array');
        }

        if(!$this->availableKeysCheckIgnored) {
            if (count($keys = array_diff(array_keys($data), $availableKeys)) > 0) {
                throw new InvalidDataException('Some keys in item are not available: ' . implode(', ', $keys));
            }
        }
    }

    /**
     * Checks if all required keys are provided
     *
     * @param array $data            original data
     * @param array $requiredKeys    list of required keys
     *
     * @throws InvalidDataException
     */
    protected function checkRequiredKeys($data, $requiredKeys)
    {
        if (!is_array($data)) {
            throw new InvalidDataException('Content is expected to be array');
        }

        foreach ($requiredKeys as $key) {
            if (!isset($data[$key])) {
                throw new InvalidDataException('Key ' . $key . ' is required');
            }
        }
    }

    /**
     * Check that at most 1 key exists
     *
     * @param $data
     * @param $keys
     * @throws \Paysera\Component\Serializer\Exception\InvalidDataException
     */
    protected function checkOnlyOneKeyExists($data, $keys)
    {
        if (!is_array($data)) {
            throw new InvalidDataException('Content is expected to be array');
        }

        $count = 0;
        foreach ($keys as $key) {
            if (isset($data[$key])) {
                $count++;
            }
            if ($count > 1) {
                throw new InvalidDataException(sprintf('Only one of %s can be provided', implode(', ', $keys)));
            }
        }
    }
} 
