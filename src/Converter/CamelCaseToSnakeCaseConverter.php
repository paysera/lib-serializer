<?php

namespace Paysera\Component\Serializer\Converter;

use Paysera\Component\Serializer\Validation\PropertyPathConverterInterface;

class CamelCaseToSnakeCaseConverter implements PropertyPathConverterInterface
{
    public function convert($path)
    {
        return ltrim(
            mb_strtolower(
                preg_replace(
                    '/[A-Z]/u',
                    '_$0',
                    $path
                )
            ),
            '_'
        );
    }
}
