<?php

namespace Paysera\Component\Serializer\Tests\Normalizer;

use Paysera\Component\Serializer\Normalizer\PlainItemNormalizer;
use PHPUnit\Framework\TestCase;

class PlainItemNormalizerTest extends TestCase
{
    /**
     * @dataProvider mapToEntityProvider
     */
    public function testMapToEntity(PlainItemNormalizer $normalizer, $data, $expected)
    {
        $this->assertSame($expected, $normalizer->mapToEntity($data));
    }

    public static function mapToEntityProvider()
    {
        return [
            'item under key' => [new PlainItemNormalizer('id', 'default'), ['id' => ['nested']], ['nested']],
            'key absent' => [new PlainItemNormalizer('id', 'default'), ['other' => 1], 'default'],
            'value null' => [new PlainItemNormalizer('id', 'default'), ['id' => null], 'default'],
            'data null' => [new PlainItemNormalizer('id', 'default'), null, 'default'],
            'no default given' => [new PlainItemNormalizer('id'), [], null],
        ];
    }
}
