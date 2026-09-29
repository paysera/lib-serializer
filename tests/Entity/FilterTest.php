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

    /**
     * @dataProvider orderingProvider
     */
    public function testOrdering(Filter $filter, array $expected)
    {
        $this->assertSame(
            $expected,
            [
                'orderBy' => $filter->getOrderBy(),
                'orderAsc' => $filter->isOrderAsc(),
                'orderDirection' => $filter->getOrderDirection(),
            ]
        );
    }

    public static function orderingProvider()
    {
        return [
            'not set' => [new Filter(), ['orderBy' => null, 'orderAsc' => null, 'orderDirection' => 'DESC']],
            'ascending' => [
                (new Filter())->setOrderBy('created_at')->setOrderAsc(true),
                ['orderBy' => 'created_at', 'orderAsc' => true, 'orderDirection' => 'ASC'],
            ],
            'descending' => [
                (new Filter())->setOrderAsc(false),
                ['orderBy' => null, 'orderAsc' => false, 'orderDirection' => 'DESC'],
            ],
        ];
    }

    public function testOrderingSettersAreFluent()
    {
        $filter = new Filter();

        $this->assertSame($filter, $filter->setOrderBy('created_at'));
        $this->assertSame($filter, $filter->setOrderAsc(true));
    }

    /**
     * @dataProvider filterClassProvider
     */
    public function testCreateReturnsNewInstanceOfCalledClass($class)
    {
        $filter = $class::create();

        $this->assertEquals(new $class(), $filter);
        $this->assertNotSame($filter, $class::create());
        $this->assertSame(0, $filter->getOffset());
    }

    public static function filterClassProvider()
    {
        return [
            'filter' => [Filter::class],
            'subclass with its own constructor' => [OwnConstructorFilter::class],
        ];
    }
}
