<?php

namespace Paysera\Component\Serializer\Tests\Encoding;

use Paysera\Component\Serializer\Encoding\Json;
use Paysera\Component\Serializer\Exception\EncodingException;
use PHPUnit\Framework\TestCase;

class JsonTest extends TestCase
{
    public function testDecodeReturnsAssociativeArrays()
    {
        $this->assertSame(
            ['a' => 1, 'b' => ['c' => true], 'd' => [1, 2]],
            (new Json())->decode('{"a":1,"b":{"c":true},"d":[1,2]}')
        );
    }

    public function testDecodeReturnsScalars()
    {
        $this->assertSame('text', (new Json())->decode('"text"'));
        $this->assertNull((new Json())->decode('null'));
    }

    public function testDecodeThrowsOnInvalidJson()
    {
        $this->expectException(EncodingException::class);
        // The message carries the input that failed to decode; dropping it would change what callers log.
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
