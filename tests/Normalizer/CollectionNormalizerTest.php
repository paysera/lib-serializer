<?php

namespace Paysera\Component\Serializer\Tests\Normalizer;

use ArrayIterator;
use Paysera\Component\Serializer\Normalizer\CollectionNormalizer;
use Paysera\Component\Serializer\Normalizer\NormalizerInterface;
use PHPUnit\Framework\TestCase;

class CollectionNormalizerTest extends TestCase
{
    /**
     * @dataProvider collectionProvider
     */
    public function testMapFromEntity($collection, array $expected)
    {
        $inner = $this->createMock(NormalizerInterface::class);
        $inner->expects($this->exactly(count($expected['items'])))
            ->method('mapFromEntity')
            ->willReturnCallback(function ($item) {
                return ['id' => $item];
            });

        $this->assertSame($expected, (new CollectionNormalizer($inner))->mapFromEntity($collection));
    }

    public static function collectionProvider()
    {
        return [
            'items' => [
                new ArrayIterator(['a' => 1, 'b' => 2, 'c' => 3]),
                [
                    'items' => [['id' => 1], ['id' => 2], ['id' => 3]],
                    '_metadata' => [
                        'total' => 3,
                        'offset' => 0,
                        'limit' => null,
                    ],
                ],
            ],
            'empty' => [
                [],
                [
                    'items' => [],
                    '_metadata' => [
                        'total' => 0,
                        'offset' => 0,
                        'limit' => null,
                    ],
                ],
            ],
        ];
    }
}
