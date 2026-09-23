<?php

namespace Paysera\Component\Serializer\Tests\Encoding;

use JsonpCallbackValidator;
use Paysera\Component\Serializer\Encoding\Json;
use Paysera\Component\Serializer\Encoding\Jsonp;
use Paysera\Component\Serializer\Exception\EncodingException;
use PHPUnit\Framework\TestCase;

class JsonpTest extends TestCase
{
    public function testEncodeWrapsJsonInCallback()
    {
        $encoder = new Jsonp(new Json(), new JsonpCallbackValidator(), 'handle');

        $this->assertSame('/**/handle({"a":1});', $encoder->encode(['a' => 1]));
    }

    public function testEncodeAppendsValidJsonParameter()
    {
        $encoder = new Jsonp(new Json(), new JsonpCallbackValidator(), 'app.handle', '{"id":5}');

        $this->assertSame('/**/app.handle({"a":1}, {"id":5});', $encoder->encode(['a' => 1]));
    }

    public function testEncodeReplacesDataWithErrorWhenParameterIsNotJson()
    {
        $encoder = new Jsonp(new Json(), new JsonpCallbackValidator(), 'handle', 'not json');

        $this->assertSame(
            '/**/handle({"error":"invalid_parameters","error_description":"Passed parameter must be valid JSON string"});',
            $encoder->encode(['a' => 1])
        );
    }

    /**
     * @dataProvider invalidCallbackProvider
     */
    public function testEncodeReturnsAlertForInvalidCallback($callback)
    {
        $encoder = new Jsonp(new Json(), new JsonpCallbackValidator(), $callback, '{"id":5}');

        $this->assertSame('alert("Invalid callback function name");', $encoder->encode(['a' => 1]));
    }

    public static function invalidCallbackProvider()
    {
        return [
            'script injection' => ['alert(1)//'],
            'reserved keyword' => ['function'],
            'empty' => [''],
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
