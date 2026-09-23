<?php

namespace Paysera\Component\Serializer\Tests\Entity;

use Paysera\Component\Serializer\Entity\Violation;
use PHPUnit\Framework\TestCase;

class ViolationTest extends TestCase
{
    public function testFieldsDefaultToNull()
    {
        $violation = new Violation();

        $this->assertNull($violation->getField());
        $this->assertNull($violation->getCode());
        $this->assertNull($violation->getMessage());
    }

    public function testSettersAreFluentAndStoreValues()
    {
        $violation = new Violation();

        $this->assertSame($violation, $violation->setField('email'));
        $this->assertSame($violation, $violation->setCode('not_blank'));
        $this->assertSame($violation, $violation->setMessage('This value should not be blank.'));

        $this->assertSame('email', $violation->getField());
        $this->assertSame('not_blank', $violation->getCode());
        $this->assertSame('This value should not be blank.', $violation->getMessage());
    }
}
