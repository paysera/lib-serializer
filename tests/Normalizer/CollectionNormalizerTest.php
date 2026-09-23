<?php

namespace Paysera\Component\Serializer\Tests\Normalizer;

use ArrayIterator;
use Paysera\Component\Serializer\Normalizer\CollectionNormalizer;
use Paysera\Component\Serializer\Normalizer\NormalizerInterface;
use PHPUnit\Framework\TestCase;

class CollectionNormalizerTest extends TestCase
{
    public function testMapFromEntityRendersItemsAndMetadata()
    {
        $inner = $this->createMock(NormalizerInterface::class);
        $inner->method('mapFromEntity')->willReturnCallback(function ($item) {
            return ['id' => $item];
        });

        $this->assertSame(
            [
                'items' => [['id' => 1], ['id' => 2], ['id' => 3]],
                '_metadata' => [
                    'total' => 3,
                    'offset' => 0,
                    'limit' => null,
                ],
            ],
            (new CollectionNormalizer($inner))->mapFromEntity(new ArrayIterator(['a' => 1, 'b' => 2, 'c' => 3]))
        );
    }

    public function testMapFromEntityRendersEmptyCollection()
    {
        $inner = $this->createMock(NormalizerInterface::class);
        $inner->expects($this->never())->method('mapFromEntity');

        $this->assertSame(
            [
                'items' => [],
                '_metadata' => [
                    'total' => 0,
                    'offset' => 0,
                    'limit' => null,
                ],
            ],
            (new CollectionNormalizer($inner))->mapFromEntity([])
        );
    }
}
