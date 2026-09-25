<?php

namespace Paysera\Component\Serializer\Tests\Accessor;

use Paysera\Component\Serializer\Accessor\PropertyPathFieldAccessor;
use Paysera\Component\Serializer\Tests\Fixtures\Accessor\Payee;
use Paysera\Component\Serializer\Tests\Fixtures\Accessor\Payment;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyPath;

class PropertyPathFieldAccessorTest extends TestCase
{
    /**
     * @dataProvider getValueProvider
     */
    public function testGetValue($propertyPath, $entity, $expected)
    {
        $accessor = new PropertyPathFieldAccessor(PropertyAccess::createPropertyAccessor(), $propertyPath);

        $this->assertSame($expected, $accessor->getValue($entity));
    }

    public static function getValueProvider()
    {
        return [
            'nested property' => ['beneficiary.fullName', new Payment(new Payee('Jane Doe')), 'Jane Doe'],
            'property path object' => [
                new PropertyPath('beneficiary.fullName'),
                new Payment(new Payee('Jane Doe')),
                'Jane Doe',
            ],
            'array indexes' => ['[account][number]', ['account' => ['number' => 'LT12 1000']], 'LT12 1000'],
            'missing array index' => ['[account][number]', ['account' => []], null],
        ];
    }

    /**
     * @dataProvider setValueProvider
     */
    public function testSetValueWritesANestedProperty($propertyPath)
    {
        $accessor = new PropertyPathFieldAccessor(PropertyAccess::createPropertyAccessor(), $propertyPath);
        $payment = new Payment(new Payee('Jane Doe'));

        $accessor->setValue($payment, 'John Doe');

        $this->assertEquals(new Payment(new Payee('John Doe')), $payment);
    }

    public static function setValueProvider()
    {
        return [
            'string path' => ['beneficiary.fullName'],
            'property path object' => [new PropertyPath('beneficiary.fullName')],
        ];
    }
}
