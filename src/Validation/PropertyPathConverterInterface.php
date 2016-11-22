<?php

namespace Paysera\Component\Serializer\Validation;

interface PropertyPathConverterInterface
{
    /**
     * @param string $path
     *
     * @return string
     */
    public function convert($path);
}
