<?php

namespace Paysera\Component\Serializer\Tests\Accessor;

use Paysera\Component\Serializer\Accessor\PropertyPathFieldAccessor;
use Paysera\Component\Serializer\Tests\Fixtures\Accessor\Payee;
use Paysera\Component\Serializer\Tests\Fixtures\Accessor\Payment;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyPath;

/**
 * Runs against Symfony's real property accessor on every Symfony line the library allows, because this class is where
 * the library meets symfony/property-access.
 */
class PropertyPathFieldAccessorTest extends TestCase
{
    public function testGetValueReadsANestedProperty()
    {
        $accessor = new PropertyPathFieldAccessor(PropertyAccess::createPropertyAccessor(), 'beneficiary.fullName');

        $this->assertSame('Jane Doe', $accessor->getValue(new Payment(new Payee('Jane Doe'))));
    }

    public function testSetValueWritesANestedProperty()
    {
        $accessor = new PropertyPathFieldAccessor(PropertyAccess::createPropertyAccessor(), 'beneficiary.fullName');
        $payment = new Payment(new Payee('Jane Doe'));

        $accessor->setValue($payment, 'John Doe');

        $this->assertSame('John Doe', $payment->getBeneficiary()->getFullName());
    }

    public function testGetValueReadsArrayIndexes()
    {
        $accessor = new PropertyPathFieldAccessor(PropertyAccess::createPropertyAccessor(), '[account][number]');

        $this->assertSame('LT12 1000', $accessor->getValue(['account' => ['number' => 'LT12 1000']]));
    }

    public function testGetValueOfAMissingArrayIndexIsNull()
    {
        $accessor = new PropertyPathFieldAccessor(PropertyAccess::createPropertyAccessor(), '[account][number]');

        $this->assertNull($accessor->getValue(['account' => []]));
    }

    public function testAcceptsAPropertyPathObject()
    {
        $accessor = new PropertyPathFieldAccessor(
            PropertyAccess::createPropertyAccessor(),
            new PropertyPath('beneficiary.fullName')
        );
        $payment = new Payment(new Payee('Jane Doe'));

        $accessor->setValue($payment, 'John Doe');

        $this->assertSame('John Doe', $accessor->getValue($payment));
    }
}
