<?php

namespace Paysera\Component\Serializer\Tests\Factory;

use JsonpCallbackValidator;
use Paysera\Component\Serializer\Encoding\Json;
use Paysera\Component\Serializer\Encoding\Jsonp;
use Paysera\Component\Serializer\Factory\JsonpEncoderFactory;
use PHPUnit\Framework\TestCase;

class JsonpEncoderFactoryTest extends TestCase
{
    public function testCreateEncoderDefaultsToCallbackNamedCallbackWithoutParameter()
    {
        $encoder = (new JsonpEncoderFactory(new Json(), new JsonpCallbackValidator()))->createEncoder([]);

        $this->assertInstanceOf(Jsonp::class, $encoder);
        $this->assertSame('/**/callback([1]);', $encoder->encode([1]));
    }

    public function testCreateEncoderUsesCallbackAndParameterOptions()
    {
        $encoder = (new JsonpEncoderFactory(new Json(), new JsonpCallbackValidator()))->createEncoder([
            'callback' => 'handle',
            'parameter' => '{"id":5}',
        ]);

        $this->assertSame('/**/handle([1], {"id":5});', $encoder->encode([1]));
    }
}
