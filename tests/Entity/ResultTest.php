<?php

namespace Paysera\Component\Serializer\Tests\Entity;

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
}
