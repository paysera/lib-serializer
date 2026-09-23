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
    public function testMapToEntityMapsEveryElementAndDropsKeys()
    {
        $inner = $this->createMock(DenormalizerInterface::class);
        $inner->method('mapToEntity')->willReturnCallback(function ($element) {
            return strtoupper($element);
        });

        $this->assertSame(['A', 'B'], (new ArrayNormalizer($inner))->mapToEntity(['x' => 'a', 'y' => 'b']));
    }

    public function testMapToEntityReturnsEmptyArrayForNull()
    {
        $inner = $this->createMock(DenormalizerInterface::class);
        $inner->expects($this->never())->method('mapToEntity');

        $this->assertSame([], (new ArrayNormalizer($inner))->mapToEntity(null));
    }

    public function testMapFromEntityPassesSameContextToEveryElement()
    {
        $context = new NormalizationContext();
        $inner = $this->createMock(ContextAwareNormalizerInterface::class);
        $inner->expects($this->exactly(2))
            ->method('mapFromEntity')
            ->with($this->anything(), $this->identicalTo($context))
            ->willReturnCallback(function ($element) {
                return $element * 10;
            });

        $this->assertSame(
            [10, 20],
            (new ArrayNormalizer($inner))->mapFromEntity(new ArrayIterator(['x' => 1, 'y' => 2]), $context)
        );
    }

    public function testMapFromEntityPassesNullContextWhenNoneGiven()
    {
        $inner = $this->createMock(ContextAwareNormalizerInterface::class);
        $inner->expects($this->once())
            ->method('mapFromEntity')
            ->with(1, null)
            ->willReturn('one');

        $this->assertSame(['one'], (new ArrayNormalizer($inner))->mapFromEntity([1]));
    }

    public function testMapFromEntityReturnsEmptyArrayForNull()
    {
        $inner = $this->createMock(ContextAwareNormalizerInterface::class);
        $inner->expects($this->never())->method('mapFromEntity');

        $this->assertSame([], (new ArrayNormalizer($inner))->mapFromEntity(null));
    }
}
