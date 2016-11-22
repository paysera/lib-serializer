<?php

namespace Paysera\Component\Serializer\Factory;

use Paysera\Component\Serializer\Encoding\Plain;

class EncoderFactory
{
    public function createPlainTextEncoder()
    {
        return new Plain('text/plain');
    }

    public function createPngEncoder()
    {
        return new Plain('image/png');
    }

    public function createJpegEncoder()
    {
        return new Plain('image/jpeg');
    }

    public function createGifEncoder()
    {
        return new Plain('image/gif');
    }
}
