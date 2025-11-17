<?php

namespace Paysera\Component\Serializer\Exception;

use Paysera\Component\Serializer\Entity\Violation;
use Exception;

class InvalidDataException extends Exception
{
    /**
     * @var string
     */
    private $customCode;

    /**
     * @var mixed
     */
    private $properties;

    /**
     * @var array
     */
    private $violations;

    public function __construct($message = '', $customCode = null, ?Exception $previous = null)
    {
        $this->customCode = $customCode;
        $this->violations = [];

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
     * @return Violation[]
     */
    public function getViolations()
    {
        return $this->violations;
    }

    /**
     * @param Violation[] $violations
     *
     * @return $this
     */
    public function setViolations(array $violations)
    {
        $this->violations = $violations;

        return $this;
    }

    /**
     * @param Violation $violation
     *
     * @return $this
     */
    public function addViolation(Violation $violation)
    {
        $this->violations[] = $violation;

        return $this;
    }
}
