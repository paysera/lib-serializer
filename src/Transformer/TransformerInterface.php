<?php

namespace Paysera\Component\Serializer\Transformer;

interface TransformerInterface
{
    /**
     * Transforms entity object to another (similar) object
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function transform($data);
}
