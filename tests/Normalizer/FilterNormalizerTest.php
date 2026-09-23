<?php

namespace Paysera\Component\Serializer\Tests\Normalizer;

use Paysera\Component\Serializer\Entity\Filter;
use Paysera\Component\Serializer\Normalizer\FilterNormalizer;
use PHPUnit\Framework\TestCase;
use Paysera\Component\Serializer\Exception\InvalidDataException;

class FilterNormalizerTest extends TestCase
{
    /**
     * @param Filter $filter
     * @param array $expected
     *
     * @dataProvider mapFromEntityDataProvider
     */
    public function testMapFromEntity(Filter $filter, array $expected)
    {
        $this->assertEquals($expected, (new FilterNormalizer())->mapFromEntity($filter));
    }

    /**
     * @param array $data
     * @param Filter $expected
     *
     * @dataProvider mapToEntityDataProvider
     */
    public function testMapToEntity(array $data, Filter $expected)
    {
        $this->assertEquals($expected, (new FilterNormalizer())->mapToEntity($data));
    }

    /**
     * @param array $data
     *
     * @dataProvider mapToEntityThrowExceptionDataProvider
     */
    public function testMapToEntityThrowException(array $data)
    {
        $this->expectException(InvalidDataException::class);
        (new FilterNormalizer())->mapToEntity($data);
    }

    public function mapFromEntityDataProvider()
    {
        return [
            [
                (new Filter())
                    ->setAfter("10")
                ,
                [
                    'after' => "10",
                ],
            ],
            [
                (new Filter())
                    ->setBefore("15")
                ,
                [
                    'before' => "15",
                ],
            ],
            [
                (new Filter())
                    ->setOffset(5)
                ,
                [
                    'offset' => 5,
                ],
            ],
            [
                (new Filter())
                    ->setAfter("50")
                    ->setLimit(50)
                ,
                [
                    'after' => "50",
                    'limit' => 50,
                ],
            ],
            [
                (new Filter())
                    ->setBefore("25")
                    ->setLimit(25)
                ,
                [
                    'before' => "25",
                    'limit' => 25,
                ],
            ],
            [
                (new Filter())
                    ->setOffset(2)
                    ->setLimit(4)
                ,
                [
                    'offset' => 2,
                    'limit' => 4,
                ],
            ],
        ];
    }

    public function mapToEntityDataProvider()
    {
        return [
            [
                [
                    'before' => 10,
                ],
                (new Filter())
                    ->setLimit(20)
                    ->setBefore(10)
                ,
            ],
            [
                [
                    'after' => 75,
                ],
                (new Filter())
                    ->setLimit(20)
                    ->setAfter(75)
                ,
            ],
            [
                [
                    'offset' => 40,
                ],
                (new Filter())
                    ->setLimit(20)
                    ->setOffset(40)
                ,
            ],
            [
                [
                    'before' => 10,
                    'limit' => 15,
                ],
                (new Filter())
                    ->setLimit(15)
                    ->setBefore(10)
                ,
            ],
            [
                [
                    'after' => 75,
                    'limit' => 35,
                ],
                (new Filter())
                    ->setLimit(35)
                    ->setAfter(75)
                ,
            ],
            [
                [
                    'offset' => 40,
                    'limit' => 75,
                ],
                (new Filter())
                    ->setLimit(75)
                    ->setOffset(40)
                ,
            ],
        ];
    }

    public function mapToEntityThrowExceptionDataProvider()
    {
        return [
            [
                [
                    'before' => 10,
                    'after' => 10,
                ],
            ],
            [
                [
                    'before' => 10,
                    'offset' => 10,
                ],
            ],
            [
                [
                    'after' => 10,
                    'offset' => 10,
                ],
            ],
            [
                [
                'before' => 10,
                'after' => 10,
                'offset' => 10,
                ],
            ],
        ];
    }

    public function testMapFromEntityIncludesOrdering()
    {
        $this->assertSame(
            ['offset' => 0, 'order_by' => 'name', 'order_direction' => 'asc'],
            (new FilterNormalizer())->mapFromEntity((new Filter())->setOrderBy('name')->setOrderAsc(true))
        );
        $this->assertSame(
            ['offset' => 0, 'order_direction' => 'desc'],
            (new FilterNormalizer())->mapFromEntity((new Filter())->setOrderAsc(false))
        );
    }

    public function testMapToEntityCastsNumericStringsAndMapsOrdering()
    {
        $filter = (new FilterNormalizer(['name', 'created_at']))->mapToEntity([
            'limit' => '15',
            'offset' => '30',
            'order_by' => 'created_at',
            'order_direction' => 'asc',
        ]);

        $this->assertSame(15, $filter->getLimit());
        $this->assertSame(30, $filter->getOffset());
        $this->assertSame('created_at', $filter->getOrderBy());
        $this->assertTrue($filter->isOrderAsc());
    }

    public function testMapToEntityAcceptsOrderDirectionInAnyCase()
    {
        $this->assertFalse((new FilterNormalizer(['name']))->mapToEntity(['order_direction' => 'DeSc'])->isOrderAsc());
    }

    public function testMapToEntityAcceptsLimitBoundaries()
    {
        $this->assertSame(0, (new FilterNormalizer())->mapToEntity(['limit' => 0])->getLimit());
        $this->assertSame(200, (new FilterNormalizer())->mapToEntity(['limit' => 200])->getLimit());
    }

    public function testMapToEntityUsesConfiguredLimits()
    {
        $normalizer = new FilterNormalizer([], 10, 50);

        $this->assertSame(10, $normalizer->mapToEntity([])->getLimit());

        $this->expectException(InvalidDataException::class);
        $this->expectExceptionMessage('limit cannot exceed 50');
        $normalizer->mapToEntity(['limit' => 51]);
    }

    public function testMapToEntityIgnoresEmptyOrdering()
    {
        $filter = (new FilterNormalizer())->mapToEntity(['order_by' => '', 'order_direction' => '']);

        $this->assertNull($filter->getOrderBy());
        $this->assertNull($filter->isOrderAsc());
    }

    public function testMapToEntityRejectsOrderDirectionWithoutOrderByFields()
    {
        $this->expectException(InvalidDataException::class);
        $this->expectExceptionMessage('order_direction is unsupported for this method');

        (new FilterNormalizer())->mapToEntity(['order_direction' => 'asc']);
    }

    /**
     * @dataProvider invalidDataProvider
     */
    public function testMapToEntityRejectsInvalidData(array $data, $message)
    {
        $this->expectException(InvalidDataException::class);
        $this->expectExceptionMessage($message);

        (new FilterNormalizer(['name']))->mapToEntity($data);
    }

    public static function invalidDataProvider()
    {
        return [
            'cursor with offset' => [['after' => 'a', 'offset' => 1], 'Only one cursor is supported'],
            'non-numeric limit' => [['limit' => 'ten'], 'Invalid parameter: limit'],
            'fractional limit' => [['limit' => '1.5'], 'Invalid parameter: limit'],
            'limit above maximum' => [['limit' => 201], 'limit cannot exceed 200'],
            'negative limit' => [['limit' => -1], 'limit cannot be negative'],
            'non-numeric offset' => [['offset' => 'first'], 'Invalid parameter: offset'],
            'negative offset' => [['offset' => '-5'], 'offset cannot be negative'],
            'unsupported order_by' => [['order_by' => 'email'], 'Unsupported order_by value'],
            'invalid order_direction' => [['order_direction' => 'up'], 'Invalid order_direction value'],
        ];
    }
}
