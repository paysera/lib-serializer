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
     */
    public function setProperties($properties)
    {
        $this->properties = $properties;
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
}
