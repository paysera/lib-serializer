<?php

namespace Paysera\Component\Serializer\Tests\Encoding;

use Paysera\Component\Serializer\Encoding\Json;
use Paysera\Component\Serializer\Exception\EncodingException;
use PHPUnit\Framework\TestCase;

class JsonTest extends TestCase
{
    /**
     * @dataProvider decodeProvider
     */
    public function testDecode($json, $expected)
    {
        $this->assertSame($expected, (new Json())->decode($json));
    }

    public static function decodeProvider()
    {
        return [
            'objects as associative arrays' => [
                '{"a":1,"b":{"c":true},"d":[1,2]}',
                ['a' => 1, 'b' => ['c' => true], 'd' => [1, 2]],
            ],
            'string' => ['"text"', 'text'],
            'null' => ['null', null],
        ];
    }

    public function testDecodeThrowsOnInvalidJson()
    {
        $this->expectException(EncodingException::class);
        $this->expectExceptionMessage('Cannot decode the data. Error: ' . JSON_ERROR_SYNTAX . ', JSON: {"a":');

        (new Json())->decode('{"a":');
    }

    public function testEncode()
    {
        $this->assertSame('{"a":1,"b":[1,2],"c":null}', (new Json())->encode(['a' => 1, 'b' => [1, 2], 'c' => null]));
    }

    public function testEncodeThrowsOnMalformedUtf8()
    {
        $this->expectException(EncodingException::class);
        $this->expectExceptionMessage('Cannot encode the data. JSON error: ' . JSON_ERROR_UTF8);

        (new Json())->encode(['a' => "\xB1\x31"]);
    }

    public function testContentType()
    {
        $this->assertSame('application/json', (new Json())->getContentType());
    }
}
