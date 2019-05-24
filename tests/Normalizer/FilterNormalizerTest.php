<?php

namespace Paysera\Component\Serializer\Tests\Filter;

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
}
