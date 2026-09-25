<?php

namespace Paysera\Component\Serializer\Tests\Encoding;

use Paysera\Component\Serializer\Encoding\Plain;
use PHPUnit\Framework\TestCase;

class PlainTest extends TestCase
{
    /**
     * @dataProvider contentTypeProvider
     */
    public function testContentType(Plain $plain, $expected)
    {
        $this->assertSame($expected, $plain->getContentType());
    }

    public static function contentTypeProvider()
    {
        return [
            'default' => [new Plain(), 'text/plain'],
            'configured' => [new Plain('image/png'), 'image/png'],
        ];
    }

    /**
     * @dataProvider inputProvider
     */
    public function testEncodeAndDecodeReturnInputUnchanged($input)
    {
        $plain = new Plain();

        $this->assertSame($input, $plain->encode($input));
        $this->assertSame($input, $plain->decode($input));
    }

    public static function inputProvider()
    {
        return [
            'binary string' => ["raw \x00 bytes"],
            'array' => [['not', 'a', 'string']],
        ];
    }
}
