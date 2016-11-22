<?php

namespace Paysera\Component\Serializer\Encoding;

use Paysera\Component\Serializer\Exception\EncodingException;

class Plain implements DecoderInterface, EncoderInterface
{
    /**
     * @var string
     */
    protected $contentType;

    public function __construct($contentType = 'text/plain')
    {
        $this->contentType = $contentType;
    }

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
        return $text;
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
        return $data;
    }

    /**
     * Returns Content-Type of this encoder
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }
}
