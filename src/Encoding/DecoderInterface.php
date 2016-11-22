<?php

namespace Paysera\Component\Serializer\Encoding;

use Paysera\Component\Serializer\Exception\EncodingException;

interface DecoderInterface
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
    public function decode($text);
}
