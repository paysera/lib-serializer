<?php

namespace Paysera\Component\Serializer\Accessor;

interface FieldAccessorInterface
{

    public function getValue($entity);

    public function setValue($entity, $value);
}
