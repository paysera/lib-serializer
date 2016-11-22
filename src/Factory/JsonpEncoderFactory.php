<?php

namespace Paysera\Component\Serializer\Factory;

use Paysera\Component\Serializer\Encoding\EncoderInterface;
use Paysera\Component\Serializer\Encoding\Json;
use Paysera\Component\Serializer\Encoding\Jsonp;
use JsonpCallbackValidator;

class JsonpEncoderFactory implements EncoderFactoryInterface
{
    protected $jsonEncoder;
    protected $callbackValidator;

    public function __construct(Json $jsonEncoder, JsonpCallbackValidator $callbackValidator)
    {
        $this->jsonEncoder = $jsonEncoder;
        $this->callbackValidator = $callbackValidator;
    }

    /**
     * @param array $options
     *
     * @return EncoderInterface
     */
    public function createEncoder(array $options)
    {
        return new Jsonp(
            $this->jsonEncoder,
            $this->callbackValidator,
            isset($options['callback']) ? $options['callback'] : 'callback',
            isset($options['parameter']) ? $options['parameter'] : null
        );
    }
} 
