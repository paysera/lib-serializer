<?php

namespace Paysera\Component\Serializer\Normalizer;

interface NormalizerInterface
{
    /**
     * Maps some structure to raw data. Usually entity object to array
     *
     * @param mixed $entity
     *
     * @return mixed
     */
    public function mapFromEntity($entity);
} 
