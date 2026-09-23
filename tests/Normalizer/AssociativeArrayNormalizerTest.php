<?php

namespace Paysera\Component\Serializer\Tests\Normalizer;

use ArrayObject;
use Paysera\Component\Serializer\Normalizer\AssociativeArrayNormalizer;
use Paysera\Component\Serializer\Normalizer\DenormalizerInterface;
use Paysera\Component\Serializer\Normalizer\NormalizerInterface;
use PHPUnit\Framework\TestCase;

class AssociativeArrayNormalizerTest extends TestCase
{
    public function testMapFromEntityKeepsKeysInArrayObject()
    {
        $inner = $this->createMock(NormalizerInterface::class);
        $inner->method('mapFromEntity')->willReturnCallback(function ($element) {
            return $element * 10;
        });

        $result = (new AssociativeArrayNormalizer($inner))->mapFromEntity(['eur' => 1, 'usd' => 2]);

        $this->assertInstanceOf(ArrayObject::class, $result);
        $this->assertSame(['eur' => 10, 'usd' => 20], $result->getArrayCopy());
    }

    /**
     * An empty ArrayObject encodes to a JSON object, where an empty array would encode to a list.
     */
    public function testMapFromEntityReturnsEmptyArrayObjectForNull()
    {
        $inner = $this->createMock(NormalizerInterface::class);
        $inner->expects($this->never())->method('mapFromEntity');

        $result = (new AssociativeArrayNormalizer($inner))->mapFromEntity(null);

        $this->assertInstanceOf(ArrayObject::class, $result);
        $this->assertSame([], $result->getArrayCopy());
    }

    public function testMapToEntityKeepsKeys()
    {
        $inner = $this->createMock(DenormalizerInterface::class);
        $inner->method('mapToEntity')->willReturnCallback(function ($element) {
            return strtoupper($element);
        });

        $this->assertSame(
            ['first' => 'A', 'second' => 'B'],
            (new AssociativeArrayNormalizer($inner))->mapToEntity(['first' => 'a', 'second' => 'b'])
        );
    }

    public function testMapToEntityReturnsEmptyArrayForNull()
    {
        $inner = $this->createMock(DenormalizerInterface::class);
        $inner->expects($this->never())->method('mapToEntity');

        $this->assertSame([], (new AssociativeArrayNormalizer($inner))->mapToEntity(null));
    }
}
