<?php

namespace Paysera\Component\Serializer\Factory;

use Paysera\Component\Serializer\Normalizer\NormalizerInterface;

interface ResponseMapperFactoryInterface
{
    /**
     * @param array $options
     *
     * @return NormalizerInterface
     */
    public function createResponseMapper(array $options);
} 
