<?php

namespace Paysera\Component\Serializer\Normalizer;

interface DenormalizerInterface
{
    /**
     * Maps raw data to some structure. Usually array to entity object
     *
     * @param mixed $data
     *
     * @return mixed
     *
     * @throws \Paysera\Component\Serializer\Exception\InvalidDataException
     */
    public function mapToEntity($data);
}
