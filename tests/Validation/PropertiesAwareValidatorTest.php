<?php

namespace Paysera\Component\Serializer\Tests\Validation;

use Paysera\Component\Serializer\Converter\CamelCaseToSnakeCaseConverter;
use Paysera\Component\Serializer\Entity\Violation;
use Paysera\Component\Serializer\Exception\InvalidDataException;
use Paysera\Component\Serializer\Tests\Fixtures\Validation\Account;
use Paysera\Component\Serializer\Tests\Fixtures\Validation\Beneficiary;
use Paysera\Component\Serializer\Tests\Fixtures\Validation\CodedValues;
use Paysera\Component\Serializer\Tests\Fixtures\Validation\GroupedAccount;
use Paysera\Component\Serializer\Tests\Fixtures\Validation\PropertyNamedConstraint;
use Paysera\Component\Serializer\Tests\Fixtures\Validation\UnnamedConstraint;
use Paysera\Component\Serializer\Validation\PropertiesAwareValidator;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validation;

/**
 * Runs against Symfony's real validator on every Symfony line the library allows, because this class is where the
 * library meets symfony/validator.
 */
class PropertiesAwareValidatorTest extends TestCase
{
    public function testValidEntityPassesWithoutException()
    {
        $validator = new PropertiesAwareValidator($this->createSymfonyValidator(), new CamelCaseToSnakeCaseConverter());

        $validator->validate(new Account('LT12 1000 0111 0100 1000', '', new Beneficiary('Jane Doe')));

        $this->addToAssertionCount(1);
    }

    public function testViolationsAreReportedByConvertedPropertyPath()
    {
        $validator = new PropertiesAwareValidator($this->createSymfonyValidator(), new CamelCaseToSnakeCaseConverter());

        $exception = $this->validateAndCatch($validator, new Account('', 'jd', new Beneficiary('')));

        $properties = $exception->getProperties();
        ksort($properties);
        $this->assertSame(
            [
                'account_number' => ['This value should not be blank.'],
                'beneficiary.full_name' => ['This value should not be blank.'],
                'nickname' => ['This value should be blank.'],
            ],
            $properties
        );
        $this->assertSame(
            [
                'account_number' => ['This value should not be blank.', 'is_blank'],
                'beneficiary.full_name' => ['This value should not be blank.', 'is_blank'],
                'nickname' => ['This value should be blank.', 'not_blank'],
            ],
            $this->describeViolations($exception->getViolations())
        );
    }

    public function testPropertyPathsAreKeptAsTheyAreWithoutConverter()
    {
        $validator = new PropertiesAwareValidator($this->createSymfonyValidator());

        $exception = $this->validateAndCatch($validator, new Account('', '', new Beneficiary('')));

        $properties = $exception->getProperties();
        ksort($properties);
        $this->assertSame(['accountNumber', 'beneficiary.fullName'], array_keys($properties));
    }

    public function testCustomConstraintCodesAreReportedByTheirErrorNames()
    {
        $validator = new PropertiesAwareValidator($this->createSymfonyValidator(), new CamelCaseToSnakeCaseConverter());

        $exception = $this->validateAndCatch($validator, new CodedValues());

        // Symfony reads `$errorNames` up to 6.4 and only the `ERROR_NAMES` constant on 7.x, so a constraint that names
        // its errors the old way only reports its raw code there.
        $propertyNamedCode = property_exists(Constraint::class, 'errorNames') ? 'failure' : PropertyNamedConstraint::FAILURE_ERROR;
        $this->assertSame(
            [
                'dual_named' => ['Dual named failure.', 'failure'],
                'property_named' => ['Property named failure.', $propertyNamedCode],
                'uncoded' => ['Unnamed failure.', null],
                'unnamed' => ['Unnamed failure.', UnnamedConstraint::FAILURE_ERROR],
            ],
            $this->describeViolations($exception->getViolations())
        );
    }

    public function testOnlyTheGivenGroupsAreValidated()
    {
        $validator = new PropertiesAwareValidator($this->createSymfonyValidator(), new CamelCaseToSnakeCaseConverter());

        $validator->validate(new GroupedAccount(''));

        $exception = $this->validateAndCatch($validator, new GroupedAccount(''), ['Strict']);
        $this->assertSame(['account_number' => ['This value should not be blank.']], $exception->getProperties());
    }

    public function testValidatorWithoutTheCurrentInterfaceGetsEntityAndGroups()
    {
        $entity = new stdClass();
        $legacyValidator = new class() {
            public $calls = [];

            public function validate($entity, $groups)
            {
                $this->calls[] = [$entity, $groups];

                return new ConstraintViolationList([
                    new ConstraintViolation('First message.', 'First message.', [], $entity, 'someField', 1, null, 'CODE_WITHOUT_CONSTRAINT'),
                    new ConstraintViolation('Second message.', 'Second message.', [], $entity, 'someField', 1),
                ]);
            }
        };

        $exception = $this->validateAndCatch(new PropertiesAwareValidator($legacyValidator), $entity, ['Strict']);

        $this->assertSame([[$entity, ['Strict']]], $legacyValidator->calls);
        $this->assertSame(['someField' => ['First message.', 'Second message.']], $exception->getProperties());
        $this->assertSame(
            [['someField', 'First message.', null], ['someField', 'Second message.', null]],
            array_map(function (Violation $violation) {
                return [$violation->getField(), $violation->getMessage(), $violation->getCode()];
            }, $exception->getViolations())
        );
    }

    private function createSymfonyValidator()
    {
        return Validation::createValidatorBuilder()->addMethodMapping('loadValidatorMetadata')->getValidator();
    }

    private function validateAndCatch(PropertiesAwareValidator $validator, $entity, $groups = null)
    {
        try {
            $validator->validate($entity, $groups);
        } catch (InvalidDataException $exception) {
            return $exception;
        }

        $this->fail('InvalidDataException was not thrown');
    }

    /**
     * @param Violation[] $violations
     *
     * @return array field => [message, code], sorted by field
     */
    private function describeViolations(array $violations)
    {
        $described = [];
        foreach ($violations as $violation) {
            $described[$violation->getField()] = [$violation->getMessage(), $violation->getCode()];
        }
        ksort($described);

        return $described;
    }
}
