<?php

namespace Paysera\Component\Serializer\Tests\Entity;

use BadMethodCallException;
use Paysera\Component\Serializer\Entity\Filter;
use Paysera\Component\Serializer\Entity\Result;
use Paysera\Component\Serializer\Tests\Fixtures\OwnConstructorFilter;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use ReturnTypeWillChange;

class ResultTest extends TestCase
{
    public function testIterateResultWithoutItems()
    {
        $result = new Result();

        $this->assertSame([], iterator_to_array($result));
    }

    public function testGetItemsReturnsArrayWhenItemsNotSet()
    {
        $result = new Result();

        $this->assertSame([], $result->getItems());
    }

    public function testIterateResultWithItems()
    {
        $result = (new Result())->setItems([1, 2, 3]);

        $this->assertSame([1, 2, 3], iterator_to_array($result));
    }

    public function testAddItemWithoutSettingItemsFirst()
    {
        $result = new Result();
        $result->addItem('a');

        $this->assertSame(['a'], $result->getItems());
    }

    public function testTotalCountDefaultAppliesWhenConstructorIsBypassed()
    {
        $result = (new ReflectionClass(Result::class))->newInstanceWithoutConstructor();

        $this->assertSame(0, $result->getTotalCount());
    }

    /**
     * Reflection-based instantiation is how ORM hydration, reflection serializers and
     * PHPUnit's disableOriginalConstructor() build objects. Pins the defaults to the
     * property declarations: a constructor assignment would not cover this path.
     */
    public function testItemsDefaultAppliesWhenConstructorIsBypassed()
    {
        $result = (new ReflectionClass(Result::class))->newInstanceWithoutConstructor();

        $this->assertSame([], $result->getItems());
        $this->assertSame([], iterator_to_array($result));
    }

    /**
     * Filter carries its offset default on the property declaration, so a descendant
     * that declares its own constructor without calling parent::__construct() still
     * reports 0 rather than null — which is what keeps this calculation from silently
     * skipping and reporting a total of 0 for a non-empty result set.
     */
    public function testCalculateTotalCountForFilterSubclassNotCallingParentConstructor()
    {
        $result = (new Result(new OwnConstructorFilter()))->setItems([1, 2]);

        $this->assertSame(2, $result->calculateTotalCount(2));
        $this->assertSame(2, $result->getTotalCount());
    }

    /**
     * The PHP 8.1 tentative return type notice is emitted when the class is
     * declared, not when getIterator() is called, so it cannot be caught with an
     * error handler from inside a test. Assert the declaration instead: either a
     * native return type or the attribute keeps IteratorAggregate quiet.
     */
    public function testGetIteratorSuppressesTentativeReturnTypeDeprecation()
    {
        $method = new ReflectionMethod(Result::class, 'getIterator');

        if ($method->hasReturnType()) {
            $this->assertSame('Traversable', (string)$method->getReturnType());
            return;
        }

        if (PHP_VERSION_ID < 80000) {
            $this->markTestSkipped('Attributes require PHP 8.0; the notice only exists on PHP 8.1+.');
        }

        $attributes = array_map(
            function ($attribute) {
                return $attribute->getName();
            },
            $method->getAttributes()
        );

        $this->assertContains(ReturnTypeWillChange::class, $attributes);
    }

    public function testCalculateTotalCountThrowsWithoutFilter()
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('filter must be set before calling this method');

        (new Result())->calculateTotalCount(5);
    }

    /**
     * @dataProvider totalCountProvider
     */
    public function testCalculateTotalCount(Filter $filter, $resultCount, $expectedReturn, $expectedTotal)
    {
        $result = (new Result($filter))->setTotalCount(99);

        $this->assertSame($expectedReturn, $result->calculateTotalCount($resultCount));
        $this->assertSame($expectedTotal, $result->getTotalCount());
    }

    public static function totalCountProvider()
    {
        return [
            'no limit' => [(new Filter())->setOffset(10), 5, 15, 15],
            'fewer results than limit' => [(new Filter())->setOffset(10)->setLimit(20), 5, 15, 15],
            'no results on first page' => [(new Filter())->setLimit(20), 0, 0, 0],
            'page is full' => [(new Filter())->setLimit(20), 20, null, 99],
            'cursor is used' => [(new Filter())->setAfter('cursor'), 5, null, 99],
            'no results past first page' => [(new Filter())->setOffset(40)->setLimit(20), 0, null, 99],
        ];
    }

    /**
     * @dataProvider resultProvider
     */
    public function testGetters(Result $result, array $expected)
    {
        $this->assertSame(
            $expected,
            [
                'filter' => $result->getFilter(),
                'totalCount' => $result->getTotalCount(),
                'hasNext' => $result->hasNext(),
                'hasPrevious' => $result->hasPrevious(),
                'after' => $result->getAfter(),
                'before' => $result->getBefore(),
            ]
        );
    }

    public static function resultProvider()
    {
        $filter = new Filter();

        return [
            'not set' => [
                new Result($filter),
                [
                    'filter' => $filter,
                    'totalCount' => 0,
                    'hasNext' => null,
                    'hasPrevious' => null,
                    'after' => null,
                    'before' => null,
                ],
            ],
            'all set, total count given as a string' => [
                (new Result($filter))
                    ->setTotalCount('12')
                    ->setHasNext(true)
                    ->setHasPrevious(false)
                    ->setAfter('a')
                    ->setBefore('b'),
                [
                    'filter' => $filter,
                    'totalCount' => 12,
                    'hasNext' => true,
                    'hasPrevious' => false,
                    'after' => 'a',
                    'before' => 'b',
                ],
            ],
        ];
    }
}
