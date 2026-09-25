<?php

namespace Paysera\Component\Serializer\Tests\Encoding;

use JsonpCallbackValidator;
use Paysera\Component\Serializer\Encoding\Json;
use Paysera\Component\Serializer\Encoding\Jsonp;
use Paysera\Component\Serializer\Exception\EncodingException;
use PHPUnit\Framework\TestCase;

class JsonpTest extends TestCase
{
    /**
     * @dataProvider encodeProvider
     */
    public function testEncode($callback, $parameter, $expected)
    {
        $encoder = new Jsonp(new Json(), new JsonpCallbackValidator(), $callback, $parameter);

        $this->assertSame($expected, $encoder->encode(['a' => 1]));
    }

    public static function encodeProvider()
    {
        return [
            'callback only' => ['handle', null, '/**/handle({"a":1});'],
            'valid JSON parameter' => ['app.handle', '{"id":5}', '/**/app.handle({"a":1}, {"id":5});'],
            'parameter that is not JSON' => [
                'handle',
                'not json',
                '/**/handle({"error":"invalid_parameters",'
                . '"error_description":"Passed parameter must be valid JSON string"});',
            ],
            'script injection callback' => ['alert(1)//', '{"id":5}', 'alert("Invalid callback function name");'],
            'reserved keyword callback' => ['function', '{"id":5}', 'alert("Invalid callback function name");'],
            'empty callback' => ['', '{"id":5}', 'alert("Invalid callback function name");'],
        ];
    }

    public function testEncodePropagatesJsonEncodingFailure()
    {
        $encoder = new Jsonp(new Json(), new JsonpCallbackValidator(), 'handle');

        $this->expectException(EncodingException::class);
        $encoder->encode(["\xB1\x31"]);
    }

    public function testContentType()
    {
        $encoder = new Jsonp(new Json(), new JsonpCallbackValidator(), 'handle');

        $this->assertSame('application/javascript', $encoder->getContentType());
    }
}
