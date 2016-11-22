<?php

namespace Paysera\Component\Serializer\Factory;

use Paysera\Component\Serializer\Encoding\EncoderInterface;

interface EncoderFactoryInterface
{

    /**
     * @param array $options
     *
     * @return EncoderInterface
     */
    public function createEncoder(array $options);
} 
