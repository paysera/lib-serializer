<?php

namespace Paysera\Component\Serializer\Tests\Entity;

use Paysera\Component\Serializer\Entity\Filter;
use Paysera\Component\Serializer\Entity\FollowUpFilter;
use Paysera\Component\Serializer\Tests\Fixtures\OwnConstructorFilter;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class FilterTest extends TestCase
{
    public function testOffsetDefaultsToZero()
    {
        $this->assertSame(0, (new Filter())->getOffset());
    }

    public function testOffsetDefaultsToZeroForSubclassNotCallingParentConstructor()
    {
        $this->assertSame(0, (new OwnConstructorFilter('done'))->getOffset());
    }

    public function testOffsetDefaultsToZeroWhenConstructorIsBypassed()
    {
        $filter = (new ReflectionClass(Filter::class))->newInstanceWithoutConstructor();

        $this->assertSame(0, $filter->getOffset());
    }

    public function testGetOffsetReturnsNullWhenCursorIsUsed()
    {
        $this->assertNull((new Filter())->setAfter('cursor')->getOffset());
        $this->assertNull((new Filter())->setBefore('cursor')->getOffset());
    }

    public function testFollowUpFilterInheritsOffsetDefault()
    {
        $filter = (new ReflectionClass(FollowUpFilter::class))->newInstanceWithoutConstructor();

        $this->assertSame(0, $filter->getOffset());
    }

    public function testFollowUpFilterKeepsConstructorOffset()
    {
        $this->assertSame(10, (new FollowUpFilter(5, 10))->getOffset());
    }

    public function testOrderingDefaultsToNothing()
    {
        $filter = new Filter();

        $this->assertNull($filter->getOrderBy());
        $this->assertNull($filter->isOrderAsc());
        $this->assertSame('DESC', $filter->getOrderDirection());
    }

    public function testOrderingSettersAreFluent()
    {
        $filter = new Filter();

        $this->assertSame($filter, $filter->setOrderBy('created_at'));
        $this->assertSame($filter, $filter->setOrderAsc(true));
        $this->assertSame('created_at', $filter->getOrderBy());
        $this->assertTrue($filter->isOrderAsc());
    }

    public function testOrderDirection()
    {
        $this->assertSame('ASC', (new Filter())->setOrderAsc(true)->getOrderDirection());
        $this->assertSame('DESC', (new Filter())->setOrderAsc(false)->getOrderDirection());
    }

    public function testCreateReturnsNewInstanceOfCalledClass()
    {
        $filter = Filter::create();
        $subclassFilter = OwnConstructorFilter::create();

        $this->assertInstanceOf(Filter::class, $filter);
        $this->assertNotSame($filter, Filter::create());
        $this->assertInstanceOf(OwnConstructorFilter::class, $subclassFilter);
        $this->assertSame(0, $subclassFilter->getOffset());
    }
}
