<?php

namespace Paysera\Component\Serializer\Exception;

class InvalidDataException extends \Exception
{
    /**
     * @var string
     */
    protected $customCode;

    /**
     * @var mixed
     */
    protected $properties;

    /**
     * @var array
     */
    protected $codes;

    public function __construct($message = '', $customCode = null, \Exception $previous = null)
    {
        $this->customCode = $customCode;
        parent::__construct($message, 0, $previous);
    }

    /**
     * Gets customCode
     *
     * @return string
     */
    public function getCustomCode()
    {
        return $this->customCode;
    }

    /**
     * Sets properties
     *
     * @param mixed $properties
     *
     * @return $this
     */
    public function setProperties($properties)
    {
        $this->properties = $properties;

        return $this;
    }

    /**
     * Gets properties
     *
     * @return mixed
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @return array
     */
    public function getCodes()
    {
        return $this->codes;
    }

    /**
     * @param array $codes
     *
     * @return $this
     */
    public function setCodes(array $codes)
    {
        $this->codes = $codes;

        return $this;
    }
}
