<?php

namespace Paysera\Component\Serializer\Tests\Factory;

use Paysera\Component\Serializer\Encoding\Plain;
use Paysera\Component\Serializer\Factory\EncoderFactory;
use PHPUnit\Framework\TestCase;

class EncoderFactoryTest extends TestCase
{
    /**
     * @dataProvider encoderProvider
     */
    public function testCreatesPlainEncoderWithContentType($method, $contentType)
    {
        $encoder = (new EncoderFactory())->$method();

        $this->assertInstanceOf(Plain::class, $encoder);
        $this->assertSame($contentType, $encoder->getContentType());
    }

    public static function encoderProvider()
    {
        return [
            'plain text' => ['createPlainTextEncoder', 'text/plain'],
            'png' => ['createPngEncoder', 'image/png'],
            'jpeg' => ['createJpegEncoder', 'image/jpeg'],
            'gif' => ['createGifEncoder', 'image/gif'],
        ];
    }
}
