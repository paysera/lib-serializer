<?php

namespace Paysera\Component\Serializer\Validation;

use Paysera\Component\Serializer\Exception\InvalidDataException;
use Symfony\Component\Validator\ValidatorInterface as LegacyValidatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


/**
 * Purpose:
 *     1. detect invalid properties
 *     2. throws exception with detailed information about each invalid property (name, message).
 *
 * Use case:
 *     1. front-end sends data to REST API. If data invalid, then front-end will get detailed information
 *        about each invalid property (name, message).
 */
class PropertiesAwareValidator
{
    protected $validator;
    protected $propertyPathConverter;

    /**
     * @param ValidatorInterface|LegacyValidatorInterface $validator
     * @param PropertyPathConverterInterface $propertyPathConverter
     */
    public function __construct(
        $validator,
        PropertyPathConverterInterface $propertyPathConverter = null
    ) {
        $this->validator = $validator;
        $this->propertyPathConverter = $propertyPathConverter;
    }

    /**
     * Validates entity, throws InvalidDataException if some constraint fails.
     *
     * @param object $entity
     * @param array|null $groups
     *
     * @throws InvalidDataException
     */
    public function validate($entity, $groups = null)
    {
        $violationList = $this->validator->validate($entity, $groups);
        if ($violationList->count() > 0) {
            $properties = array();

            foreach ($violationList as $violation) {
                $path = $violation->getPropertyPath();
                if ($this->propertyPathConverter !== null) {
                    $path = $this->propertyPathConverter->convert($path);
                }
                $properties[$path][] = $violation->getMessage();
            }

            $exception = new InvalidDataException();
            $exception->setProperties($properties);

            throw $exception;
        }
    }
}
