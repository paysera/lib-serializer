<?php

namespace Paysera\Component\Serializer\Tests\Normalizer;

use Paysera\Component\Serializer\Normalizer\PlainItemNormalizer;
use PHPUnit\Framework\TestCase;

class PlainItemNormalizerTest extends TestCase
{
    public function testMapToEntityReturnsItemUnderKey()
    {
        $this->assertSame(['nested'], (new PlainItemNormalizer('id', 'default'))->mapToEntity(['id' => ['nested']]));
    }

    /**
     * @dataProvider missingItemProvider
     */
    public function testMapToEntityReturnsDefaultWhenItemIsMissing($data)
    {
        $this->assertSame('default', (new PlainItemNormalizer('id', 'default'))->mapToEntity($data));
    }

    public static function missingItemProvider()
    {
        return [
            'key absent' => [['other' => 1]],
            'value null' => [['id' => null]],
            'data null' => [null],
        ];
    }

    public function testDefaultIsNull()
    {
        $this->assertNull((new PlainItemNormalizer('id'))->mapToEntity([]));
    }
}
