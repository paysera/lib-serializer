<?php

namespace Paysera\Component\Serializer\Encoding;

use Paysera\Component\Serializer\Exception\EncodingException;

class Json implements DecoderInterface, EncoderInterface
{
    /**
     * Decodes given string to a data structure
     *
     * @param string $text
     *
     * @return mixed
     *
     * @throws EncodingException    if text is invalid
     */
    public function decode($text)
    {
        $result = json_decode($text, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new EncodingException('Cannot decode the data. Error: ' . json_last_error() . ', JSON: ' . $text);
        }
        return $result;
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
        $result = json_encode($data);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new EncodingException('Cannot encode the data. JSON error: ' . json_last_error());
        }
        return $result;
    }


    /**
     * Returns Content-Type of this encoder
     *
     * @return string
     */
    public function getContentType()
    {
        return 'application/json';
    }
}
