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
        $this->assertEquals(new Plain($contentType), (new EncoderFactory())->$method());
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
