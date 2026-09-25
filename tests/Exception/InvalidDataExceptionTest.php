<?php

namespace Paysera\Component\Serializer\Tests\Exception;

use Exception;
use Paysera\Component\Serializer\Entity\Violation;
use Paysera\Component\Serializer\Exception\InvalidDataException;
use PHPUnit\Framework\TestCase;

class InvalidDataExceptionTest extends TestCase
{
    /**
     * @dataProvider exceptionProvider
     */
    public function testGetters(InvalidDataException $exception, array $expected)
    {
        $this->assertSame(
            $expected,
            [
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'customCode' => $exception->getCustomCode(),
                'properties' => $exception->getProperties(),
                'violations' => $exception->getViolations(),
                'previous' => $exception->getPrevious(),
            ]
        );
    }

    public static function exceptionProvider()
    {
        $previous = new Exception('cause');
        $first = (new Violation())->setField('first');
        $second = (new Violation())->setField('second');
        $third = (new Violation())->setField('third');
        $defaults = [
            'message' => '',
            'code' => 0,
            'customCode' => null,
            'properties' => null,
            'violations' => [],
            'previous' => null,
        ];

        return [
            'defaults' => [new InvalidDataException(), $defaults],
            'custom code kept apart from the exception code' => [
                new InvalidDataException('Invalid input', 'invalid_input', $previous),
                array_merge(
                    $defaults,
                    ['message' => 'Invalid input', 'customCode' => 'invalid_input', 'previous' => $previous]
                ),
            ],
            'properties set' => [
                (new InvalidDataException())->setProperties(['email' => ['Invalid email']]),
                array_merge($defaults, ['properties' => ['email' => ['Invalid email']]]),
            ],
            'violations replaced' => [
                (new InvalidDataException())->addViolation($first)->setViolations([$second]),
                array_merge($defaults, ['violations' => [$second]]),
            ],
            'violation appended' => [
                (new InvalidDataException())->addViolation($first)->setViolations([$second])->addViolation($third),
                array_merge($defaults, ['violations' => [$second, $third]]),
            ],
        ];
    }

    public function testSettersAreFluent()
    {
        $exception = new InvalidDataException();

        $this->assertSame($exception, $exception->setProperties(['email' => ['Invalid email']]));
        $this->assertSame($exception, $exception->setViolations([]));
        $this->assertSame($exception, $exception->addViolation(new Violation()));
    }
}
