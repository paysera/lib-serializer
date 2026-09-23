<?php

namespace Paysera\Component\Serializer\Tests\Encoding;

use Paysera\Component\Serializer\Encoding\Plain;
use PHPUnit\Framework\TestCase;

class PlainTest extends TestCase
{
    public function testContentTypeDefaultsToTextPlain()
    {
        $this->assertSame('text/plain', (new Plain())->getContentType());
    }

    public function testContentTypeIsConfigurable()
    {
        $this->assertSame('image/png', (new Plain('image/png'))->getContentType());
    }

    public function testEncodeAndDecodeReturnInputUnchanged()
    {
        $plain = new Plain();

        $this->assertSame("raw \x00 bytes", $plain->encode("raw \x00 bytes"));
        $this->assertSame("raw \x00 bytes", $plain->decode("raw \x00 bytes"));
        $this->assertSame(['not', 'a', 'string'], $plain->encode(['not', 'a', 'string']));
    }
}
