<?php

namespace Paysera\Component\Serializer\Tests\Normalizer;

use ArrayObject;
use Paysera\Component\Serializer\Normalizer\AssociativeArrayNormalizer;
use Paysera\Component\Serializer\Normalizer\DenormalizerInterface;
use Paysera\Component\Serializer\Normalizer\NormalizerInterface;
use PHPUnit\Framework\TestCase;

class AssociativeArrayNormalizerTest extends TestCase
{
    /**
     * @dataProvider mapFromEntityProvider
     */
    public function testMapFromEntity($entity, ArrayObject $expected)
    {
        $inner = $this->createMock(NormalizerInterface::class);
        $inner->expects($this->exactly(count($expected)))
            ->method('mapFromEntity')
            ->willReturnCallback(function ($element) {
                return $element * 10;
            });

        $this->assertEquals($expected, (new AssociativeArrayNormalizer($inner))->mapFromEntity($entity));
    }

    public static function mapFromEntityProvider()
    {
        return [
            'keys kept' => [['eur' => 1, 'usd' => 2], new ArrayObject(['eur' => 10, 'usd' => 20])],
            'null' => [null, new ArrayObject()],
        ];
    }

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

        $this->assertSame($expected, (new AssociativeArrayNormalizer($inner))->mapToEntity($data));
    }

    public static function mapToEntityProvider()
    {
        return [
            'keys kept' => [['first' => 'a', 'second' => 'b'], ['first' => 'A', 'second' => 'B']],
            'null' => [null, []],
        ];
    }
}
