<?php

namespace Paysera\Component\Serializer\Tests\Filter;

use Paysera\Component\Serializer\Entity\Filter;
use Paysera\Component\Serializer\Entity\Result;
use Paysera\Component\Serializer\Normalizer\ResultMetadataNormalizer;

class ResultMetadataNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param Result $result
     * @param array $expected
     *
     * @dataProvider mapFromEntityDataProvider
     */
    public function testMapFromEntity(Result $result, array $expected)
    {
        $this->assertEquals($expected, (new ResultMetadataNormalizer())->mapFromEntity($result));
    }

    public function mapFromEntityDataProvider()
    {
        return [
            [
                (new Result()),
                [
                    'total' => 0,
                    'offset' => 0,
                    'limit' => null,
                ]
            ],
            [
                (new Result())
                    ->setTotalCount(42)
                    ->setFilter((new Filter()))
                ,
                [
                    'total' => 42,
                    'offset' => 0,
                    'limit' => null,
                ]
            ],
            [
                (new Result())
                    ->setTotalCount(85)
                    ->setFilter(
                        (new Filter())
                            ->setOffset(50)
                    )
                ,
                [
                    'total' => 85,
                    'offset' => 50,
                    'limit' => null,
                ]
            ],
            [
                (new Result())
                    ->setTotalCount(45)
                    ->setFilter(
                        (new Filter())
                            ->setOffset(30)
                            ->setLimit(20)
                    )
                ,
                [
                    'total' => 45,
                    'offset' => 30,
                    'limit' => 20,
                ]
            ],
            [
                (new Result())
                    ->setTotalCount(86)
                    ->setHasNext(true)
                    ->setHasPrevious(false)
                    ->setFilter(
                        (new Filter())
                            ->setAfter(12)
                            ->setLimit(2)
                    )
                ,
                [
                    'total' => 86,
                    'limit' => 2,
                    'has_next' => true,
                    'has_previous' => false,
                ]
            ],
            [
                (new Result())
                    ->setTotalCount(74)
                    ->setHasPrevious(true)
                    ->setHasNext(false)
                    ->setBefore('someIdentifierBeforeResult')
                    ->setAfter('someIdentifierAfterResult')
                    ->setFilter(
                        (new Filter())
                            ->setBefore("abc")
                            ->setLimit(14)
                    )
                ,
                [
                    'total' => 74,
                    'limit' => 14,
                    'has_next' => false,
                    'has_previous' => true,
                    'cursors' => [
                        'after' => 'someIdentifierAfterResult',
                        'before' => 'someIdentifierBeforeResult',
                    ]
                ]
            ],
        ];
    }
}
