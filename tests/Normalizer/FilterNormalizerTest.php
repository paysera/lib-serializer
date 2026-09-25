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

    /**
     * @dataProvider mapFromEntityOrderingProvider
     */
    public function testMapFromEntityIncludesOrdering(Filter $filter, array $expected)
    {
        $this->assertSame($expected, (new FilterNormalizer())->mapFromEntity($filter));
    }

    public static function mapFromEntityOrderingProvider()
    {
        return [
            'ascending with order_by' => [
                (new Filter())->setOrderBy('name')->setOrderAsc(true),
                ['offset' => 0, 'order_by' => 'name', 'order_direction' => 'asc'],
            ],
            'descending without order_by' => [
                (new Filter())->setOrderAsc(false),
                ['offset' => 0, 'order_direction' => 'desc'],
            ],
        ];
    }

    /**
     * @dataProvider mapToEntityWithOptionsProvider
     */
    public function testMapToEntityWithOptions(FilterNormalizer $normalizer, array $data, array $expected)
    {
        $filter = $normalizer->mapToEntity($data);

        $this->assertSame(
            $expected,
            [
                'limit' => $filter->getLimit(),
                'offset' => $filter->getOffset(),
                'orderBy' => $filter->getOrderBy(),
                'orderAsc' => $filter->isOrderAsc(),
                'after' => $filter->getAfter(),
                'before' => $filter->getBefore(),
            ]
        );
    }

    public static function mapToEntityWithOptionsProvider()
    {
        $defaults = [
            'limit' => 20,
            'offset' => 0,
            'orderBy' => null,
            'orderAsc' => null,
            'after' => null,
            'before' => null,
        ];

        return [
            'numeric strings cast to integers, ordering mapped' => [
                new FilterNormalizer(['name', 'created_at']),
                ['limit' => '15', 'offset' => '30', 'order_by' => 'created_at', 'order_direction' => 'asc'],
                array_merge($defaults, ['limit' => 15, 'offset' => 30, 'orderBy' => 'created_at', 'orderAsc' => true]),
            ],
            'order direction in any case' => [
                new FilterNormalizer(['name']),
                ['order_direction' => 'DeSc'],
                array_merge($defaults, ['orderAsc' => false]),
            ],
            'lowest limit' => [new FilterNormalizer(), ['limit' => 0], array_merge($defaults, ['limit' => 0])],
            'highest limit' => [new FilterNormalizer(), ['limit' => 200], array_merge($defaults, ['limit' => 200])],
            'configured default limit' => [
                new FilterNormalizer([], 10, 50),
                [],
                array_merge($defaults, ['limit' => 10]),
            ],
            'empty ordering ignored' => [
                new FilterNormalizer(),
                ['order_by' => '', 'order_direction' => ''],
                $defaults,
            ],
        ];
    }

    /**
     * @dataProvider invalidDataProvider
     */
    public function testMapToEntityRejectsInvalidData(FilterNormalizer $normalizer, array $data, $message)
    {
        $this->expectException(InvalidDataException::class);
        $this->expectExceptionMessage($message);

        $normalizer->mapToEntity($data);
    }

    public static function invalidDataProvider()
    {
        $normalizer = new FilterNormalizer(['name']);

        return [
            'cursor with offset' => [$normalizer, ['after' => 'a', 'offset' => 1], 'Only one cursor is supported'],
            'non-numeric limit' => [$normalizer, ['limit' => 'ten'], 'Invalid parameter: limit'],
            'fractional limit' => [$normalizer, ['limit' => '1.5'], 'Invalid parameter: limit'],
            'limit above maximum' => [$normalizer, ['limit' => 201], 'limit cannot exceed 200'],
            'limit above configured maximum' => [
                new FilterNormalizer([], 10, 50),
                ['limit' => 51],
                'limit cannot exceed 50',
            ],
            'negative limit' => [$normalizer, ['limit' => -1], 'limit cannot be negative'],
            'non-numeric offset' => [$normalizer, ['offset' => 'first'], 'Invalid parameter: offset'],
            'negative offset' => [$normalizer, ['offset' => '-5'], 'offset cannot be negative'],
            'unsupported order_by' => [$normalizer, ['order_by' => 'email'], 'Unsupported order_by value'],
            'invalid order_direction' => [$normalizer, ['order_direction' => 'up'], 'Invalid order_direction value'],
            'order_direction without order_by fields' => [
                new FilterNormalizer(),
                ['order_direction' => 'asc'],
                'order_direction is unsupported for this method',
            ],
        ];
    }
}
