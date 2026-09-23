<?php

namespace Paysera\Component\Serializer\Tests\Exception;

use Exception;
use Paysera\Component\Serializer\Entity\Violation;
use Paysera\Component\Serializer\Exception\InvalidDataException;
use PHPUnit\Framework\TestCase;

class InvalidDataExceptionTest extends TestCase
{
    public function testDefaults()
    {
        $exception = new InvalidDataException();

        $this->assertSame('', $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertNull($exception->getCustomCode());
        $this->assertNull($exception->getProperties());
        $this->assertSame([], $exception->getViolations());
        $this->assertNull($exception->getPrevious());
    }

    public function testConstructorKeepsCustomCodeSeparateFromExceptionCode()
    {
        $previous = new Exception('cause');
        $exception = new InvalidDataException('Invalid input', 'invalid_input', $previous);

        $this->assertSame('Invalid input', $exception->getMessage());
        $this->assertSame('invalid_input', $exception->getCustomCode());
        $this->assertSame(0, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }

    public function testSetPropertiesIsFluent()
    {
        $exception = new InvalidDataException();

        $this->assertSame($exception, $exception->setProperties(['email' => ['Invalid email']]));
        $this->assertSame(['email' => ['Invalid email']], $exception->getProperties());
    }

    public function testSetViolationsReplacesAndAddViolationAppends()
    {
        $first = (new Violation())->setField('first');
        $second = (new Violation())->setField('second');
        $third = (new Violation())->setField('third');
        $exception = (new InvalidDataException())->addViolation($first);

        $this->assertSame($exception, $exception->setViolations([$second]));
        $this->assertSame([$second], $exception->getViolations());

        $this->assertSame($exception, $exception->addViolation($third));
        $this->assertSame([$second, $third], $exception->getViolations());
    }
}
