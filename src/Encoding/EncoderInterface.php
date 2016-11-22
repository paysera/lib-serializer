<?php

namespace Paysera\Component\Serializer\Encoding;

use Paysera\Component\Serializer\Exception\EncodingException;

interface EncoderInterface
{
    /**
     * Encodes given data structure to a string
     *
     * @param mixed $data
     *
     * @return string
     *
     * @throws EncodingException    if such data cannot be encoded to a string using this encoder
     */
    public function encode($data);

    /**
     * Returns Content-Type of this encoder
     *
     * @return string
     */
    public function getContentType();
}
