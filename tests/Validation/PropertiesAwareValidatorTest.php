<?php

namespace Paysera\Component\Serializer\Tests\Validation;

use Paysera\Component\Serializer\Converter\CamelCaseToSnakeCaseConverter;
use Paysera\Component\Serializer\Entity\Violation;
use Paysera\Component\Serializer\Exception\InvalidDataException;
use Paysera\Component\Serializer\Tests\Fixtures\Validation\Account;
use Paysera\Component\Serializer\Tests\Fixtures\Validation\Beneficiary;
use Paysera\Component\Serializer\Tests\Fixtures\Validation\CodedValues;
use Paysera\Component\Serializer\Tests\Fixtures\Validation\ConstantNamedConstraint;
use Paysera\Component\Serializer\Tests\Fixtures\Validation\GroupedAccount;
use Paysera\Component\Serializer\Tests\Fixtures\Validation\PropertyNamedConstraint;
use Paysera\Component\Serializer\Tests\Fixtures\Validation\UnnamedConstraint;
use Paysera\Component\Serializer\Validation\PropertiesAwareValidator;
use Paysera\Component\Serializer\Validation\PropertyPathConverterInterface;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use stdClass;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validation;

class PropertiesAwareValidatorTest extends TestCase
{
    /**
     * @dataProvider validEntityProvider
     */
    public function testValidEntityPassesWithoutException($entity)
    {
        $validator = new PropertiesAwareValidator($this->createSymfonyValidator(), new CamelCaseToSnakeCaseConverter());

        $this->expectNotToPerformAssertions();

        $validator->validate($entity);
    }

    public static function validEntityProvider()
    {
        return [
            'valid entity' => [new Account('LT12 1000 0111 0100 1000', '', new Beneficiary('Jane Doe'))],
            'constraint outside the default group' => [new GroupedAccount('')],
        ];
    }

    /**
     * @dataProvider invalidEntityProvider
     */
    public function testViolationsAreReported(
        ?PropertyPathConverterInterface $converter,
        $entity,
        $groups,
        array $expectedProperties,
        array $expectedViolations
    ) {
        $validator = new PropertiesAwareValidator($this->createSymfonyValidator(), $converter);

        $exception = $this->validateAndCatch($validator, $entity, $groups);

        $properties = $exception->getProperties();
        ksort($properties);
        $this->assertSame($expectedProperties, $properties);
        $this->assertSame($expectedViolations, $this->describeViolations($exception->getViolations()));
    }

    public static function invalidEntityProvider()
    {
        $symfony = new ReflectionClass(Constraint::class);
        $propertyNamedCode = $symfony->hasProperty('errorNames')
            ? 'failure'
            : PropertyNamedConstraint::FAILURE_ERROR;
        $constantNamedCode = $symfony->hasConstant('ERROR_NAMES')
            ? 'failure'
            : ConstantNamedConstraint::FAILURE_ERROR;
        $notBlank = 'This value should not be blank.';

        return [
            'property paths converted' => [
                new CamelCaseToSnakeCaseConverter(),
                new Account('', 'jd', new Beneficiary('')),
                null,
                [
                    'account_number' => [$notBlank],
                    'beneficiary.full_name' => [$notBlank],
                    'nickname' => ['This value should be blank.'],
                ],
                [
                    ['account_number', $notBlank, 'is_blank'],
                    ['beneficiary.full_name', $notBlank, 'is_blank'],
                    ['nickname', 'This value should be blank.', 'not_blank'],
                ],
            ],
            'property paths kept as they are without a converter' => [
                null,
                new Account('', '', new Beneficiary('')),
                null,
                ['accountNumber' => [$notBlank], 'beneficiary.fullName' => [$notBlank]],
                [['accountNumber', $notBlank, 'is_blank'], ['beneficiary.fullName', $notBlank, 'is_blank']],
            ],
            'only the given groups' => [
                new CamelCaseToSnakeCaseConverter(),
                new GroupedAccount(''),
                ['Strict'],
                ['account_number' => [$notBlank]],
                [['account_number', $notBlank, 'is_blank']],
            ],
            'custom constraint codes reported by their error names' => [
                new CamelCaseToSnakeCaseConverter(),
                new CodedValues(),
                null,
                [
                    'constant_named' => ['Constant named failure.'],
                    'dual_named' => ['Dual named failure.'],
                    'property_named' => ['Property named failure.'],
                    'uncoded' => ['Unnamed failure.'],
                    'unnamed' => ['Unnamed failure.'],
                ],
                [
                    ['constant_named', 'Constant named failure.', $constantNamedCode],
                    ['dual_named', 'Dual named failure.', 'failure'],
                    ['property_named', 'Property named failure.', $propertyNamedCode],
                    ['uncoded', 'Unnamed failure.', null],
                    ['unnamed', 'Unnamed failure.', UnnamedConstraint::FAILURE_ERROR],
                ],
            ],
        ];
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
                    new ConstraintViolation(
                        'First message.',
                        'First message.',
                        [],
                        $entity,
                        'someField',
                        1,
                        null,
                        'CODE_WITHOUT_CONSTRAINT'
                    ),
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
     * @return array[]
     */
    private function describeViolations(array $violations)
    {
        $described = array_map(function (Violation $violation) {
            return [$violation->getField(), $violation->getMessage(), $violation->getCode()];
        }, $violations);
        sort($described);

        return $described;
    }
}
