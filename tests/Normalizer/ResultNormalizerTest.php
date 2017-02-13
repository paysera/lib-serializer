<?php

namespace Paysera\Component\Serializer\Tests\Filter;

use Paysera\Component\Serializer\Entity\Filter;
use Paysera\Component\Serializer\Entity\Result;
use Paysera\Component\Serializer\Normalizer\PlainNormalizer;
use Paysera\Component\Serializer\Normalizer\ResultNormalizer;

class ResultNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array $data
     * @param Result $expected
     *
     * @dataProvider mapToEntityDataProvider
     */
    public function testMapToEntity(array $data, Result $expected)
    {
        $this->assertEquals($expected, (new ResultNormalizer('items', new PlainNormalizer()))->mapToEntity($data));
    }

    public function mapToEntityDataProvider()
    {
        return [
            [
                [
                    'items' => [],
                    '_metadata' => [],
                ],
                (new Result())->setFilter(new Filter())->setItems([]),
            ],
            [
                [
                    'items' => [],
                    '_metadata' => [
                        'total' => 1,
                        'offset' => 2,
                        'limit' => 3,
                        'has_next' => true,
                        'has_previous' => false,
                    ],
                ],
                (new Result())
                    ->setFilter((new Filter())->setLimit(3)->setOffset(2))
                    ->setTotalCount(1)
                    ->setHasNext(true)
                    ->setHasPrevious(false)
                    ->setItems([])
                ,
            ],
            [
                [
                    'items' => [],
                    '_metadata' => [
                        'total' => 2,
                        'limit' => 6,
                        'has_next' => false,
                        'has_previous' => true,
                        'cursors' => [
                            'before' => 'before',
                            'after' => 'after',
                        ],
                    ],
                ],
                (new Result())
                    ->setFilter((new Filter())->setLimit(6))
                    ->setTotalCount(2)
                    ->setHasNext(false)
                    ->setHasPrevious(true)
                    ->setAfter('after')
                    ->setBefore('before')
                    ->setItems([])
                ,
            ],
        ];
    }
}
