<?php

namespace Paysera\Component\Serializer\Tests\Normalizer;

use Paysera\Component\Serializer\Normalizer\PlainNormalizer;
use PHPUnit\Framework\TestCase;
use stdClass;

class PlainNormalizerTest extends TestCase
{
    /**
     * @dataProvider valueProvider
     */
    public function testReturnsValueUnchanged($value)
    {
        $normalizer = new PlainNormalizer();

        $this->assertSame($value, $normalizer->mapFromEntity($value));
        $this->assertSame($value, $normalizer->mapToEntity($value));
    }

    public static function valueProvider()
    {
        return [
            'object' => [new stdClass()],
            'array' => [['a' => 1]],
            'null' => [null],
        ];
    }
}
