<?php

namespace Paysera\Component\Serializer\Converter;

use Paysera\Component\Serializer\Validation\PropertyPathConverterInterface;

class NoOpConverter implements PropertyPathConverterInterface
{
    public function convert($path)
    {
        return $path;
    }
}
