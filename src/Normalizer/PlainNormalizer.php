<?php

namespace Paysera\Component\Serializer\Normalizer;

class PlainNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * Returns same data
     *
     * @param mixed $entity
     *
     * @return mixed
     */
    public function mapFromEntity($entity)
    {
        return $entity;
    }

    /**
     * Maps raw data to some structure. Usually array to entity object
     *
     * @param mixed $data
     *
     * @return mixed
     *
     * @throws \Paysera\Component\Serializer\Exception\InvalidDataException
     */
    public function mapToEntity($data)
    {
        return $data;
    }
}
