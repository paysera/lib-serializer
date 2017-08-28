<?php

namespace Paysera\Component\Serializer\Validation;

use Paysera\Component\Serializer\Exception\InvalidDataException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ValidatorInterface as LegacyValidatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Exception\InvalidArgumentException;


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
        if ($this->validator instanceof ValidatorInterface) {
            $violationList = $this->validator->validate($entity, null, $groups);
        } else {
            $violationList = $this->validator->validate($entity, $groups);
        }

        if ($violationList->count() > 0) {
            $properties = array();
            $codes = array();

            foreach ($violationList as $violation) {
                $path = $violation->getPropertyPath();
                if ($this->propertyPathConverter !== null) {
                    $path = $this->propertyPathConverter->convert($path);
                }
                $properties[$path][] = $violation->getMessage();

                /** @var Constraint $constraint */
                $constraint = $violation->getConstraint();
                if ($constraint !== null && $violation->getCode() !== null) {
                    try {
                        $codes[$path][] = mb_strtolower(
                            str_replace('_ERROR', '', $constraint->getErrorName($violation->getCode()))
                        );
                    } catch (InvalidArgumentException $exception) {
                        $codes[$path][] = $violation->getCode();
                    }
                }
            }

            throw (new InvalidDataException())
                ->setProperties($properties)
                ->setCodes($codes)
            ;
        }
    }
}
