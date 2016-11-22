<?php

namespace Paysera\Component\Serializer\Normalizer;

class PlainItemNormalizer implements DenormalizerInterface
{
    /**
     * @var string
     */
    protected $itemKey;

    /**
     * @var mixed
     */
    protected $default;

    /**
     * Constructs object
     *
     * @param string $itemKey
     * @param mixed  $default
     */
    public function __construct($itemKey, $default = null)
    {
        $this->itemKey = $itemKey;
        $this->default = $default;
    }

    /**
     * Returns same data
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function mapToEntity($data)
    {
        return isset($data[$this->itemKey]) ? $data[$this->itemKey] : $this->default;
    }
}
