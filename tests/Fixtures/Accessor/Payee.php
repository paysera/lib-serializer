<?php

namespace Paysera\Component\Serializer\Tests\Fixtures\Accessor;

class Payee
{
    private $fullName;

    public function __construct($fullName)
    {
        $this->fullName = $fullName;
    }

    public function getFullName()
    {
        return $this->fullName;
    }

    public function setFullName($fullName)
    {
        $this->fullName = $fullName;
    }
}
