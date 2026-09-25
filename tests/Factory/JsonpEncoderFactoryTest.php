<?php

namespace Paysera\Component\Serializer\Tests\Factory;

use JsonpCallbackValidator;
use Paysera\Component\Serializer\Encoding\Json;
use Paysera\Component\Serializer\Encoding\Jsonp;
use Paysera\Component\Serializer\Factory\JsonpEncoderFactory;
use PHPUnit\Framework\TestCase;

class JsonpEncoderFactoryTest extends TestCase
{
    /**
     * @dataProvider optionsProvider
     */
    public function testCreateEncoder(array $options, $expected)
    {
        $encoder = (new JsonpEncoderFactory(new Json(), new JsonpCallbackValidator()))->createEncoder($options);

        $this->assertInstanceOf(Jsonp::class, $encoder);
        $this->assertSame($expected, $encoder->encode([1]));
    }

    public static function optionsProvider()
    {
        return [
            'callback named callback, no parameter by default' => [[], '/**/callback([1]);'],
            'callback and parameter options' => [
                ['callback' => 'handle', 'parameter' => '{"id":5}'],
                '/**/handle([1], {"id":5});',
            ],
        ];
    }
}
