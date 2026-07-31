<?php

namespace Paysera\Component\Serializer\Normalizer;

use Paysera\Component\Serializer\Exception\InvalidDataException;

interface DenormalizerInterface
{
    /**
     * Maps raw data to some structure. Usually array to entity object
     *
     * @param mixed $data
     *
     * @return mixed
     *
     * @throws InvalidDataException
     */
    public function mapToEntity($data);
}
