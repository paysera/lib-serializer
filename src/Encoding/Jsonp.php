<?php

namespace Paysera\Component\Serializer\Encoding;

use Paysera\Component\Serializer\Exception\EncodingException;
use JsonpCallbackValidator;

class Jsonp implements EncoderInterface
{

    protected $jsonEncoder;

    protected $callbackValidator;

    /**
     * @var string
     */
    protected $callbackFunction;

    /**
     * @var string
     */
    protected $parameter;


    public function __construct(
        Json $jsonEncoder,
        JsonpCallbackValidator $callbackValidator,
        $callbackFunction,
        $parameter = null
    ) {
        $this->jsonEncoder = $jsonEncoder;
        $this->callbackValidator = $callbackValidator;
        $this->callbackFunction = $callbackFunction;
        $this->parameter = $parameter;
    }


    /**
     * Encodes given data structure to a string
     *
     * @param mixed $data
     *
     * @return string
     *
     * @throws EncodingException    if such data cannot be encoded to a string using this encoder
     */
    public function encode($data)
    {
        if (!$this->callbackValidator->validate($this->callbackFunction)) {
            return 'alert("Invalid callback function name");';
        }
        $json = $this->jsonEncoder->encode($data);

        if ($this->parameter !== null) {
            try {
                $this->jsonEncoder->decode($this->parameter);
            } catch (EncodingException $exception) {
                $this->parameter = null;
                $json = $this->jsonEncoder->encode(array(
                    'error' => 'invalid_parameters',
                    'error_description' => 'Passed parameter must be valid JSON string',
                ));
            }
        }

        return '/**/' . $this->callbackFunction
            . '(' . $json . ($this->parameter === null ? '' : ', ' . $this->parameter) . ');';
    }

    /**
     * Returns Content-Type of this encoder
     *
     * @return string
     */
    public function getContentType()
    {
        return 'application/javascript';
    }
}
