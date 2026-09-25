<?php

namespace Paysera\Component\Serializer\Tests\Normalizer;

use ArrayIterator;
use Paysera\Component\Serializer\Entity\NormalizationContext;
use Paysera\Component\Serializer\Normalizer\ArrayNormalizer;
use Paysera\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Paysera\Component\Serializer\Normalizer\DenormalizerInterface;
use PHPUnit\Framework\TestCase;

class ArrayNormalizerTest extends TestCase
{
    /**
     * @dataProvider mapToEntityProvider
     */
    public function testMapToEntity($data, array $expected)
    {
        $inner = $this->createMock(DenormalizerInterface::class);
        $inner->expects($this->exactly(count($expected)))
            ->method('mapToEntity')
            ->willReturnCallback(function ($element) {
                return strtoupper($element);
            });

        $this->assertSame($expected, (new ArrayNormalizer($inner))->mapToEntity($data));
    }

    public static function mapToEntityProvider()
    {
        return [
            'every element, keys dropped' => [['x' => 'a', 'y' => 'b'], ['A', 'B']],
            'null' => [null, []],
        ];
    }

    /**
     * @dataProvider mapFromEntityProvider
     */
    public function testMapFromEntity(array $arguments, array $expected)
    {
        $inner = $this->createMock(ContextAwareNormalizerInterface::class);
        $inner->expects($this->exactly(count($expected)))
            ->method('mapFromEntity')
            ->with($this->anything(), $this->identicalTo($arguments[1] ?? null))
            ->willReturnCallback(function ($element) {
                return $element * 10;
            });

        $this->assertSame($expected, (new ArrayNormalizer($inner))->mapFromEntity(...$arguments));
    }

    public static function mapFromEntityProvider()
    {
        return [
            'same context for every element, keys dropped' => [
                [new ArrayIterator(['x' => 1, 'y' => 2]), new NormalizationContext()],
                [10, 20],
            ],
            'no context given' => [[[1]], [10]],
            'null' => [[null], []],
        ];
    }
}
