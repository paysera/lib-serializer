<?php

namespace Paysera\Component\Serializer\Tests\Normalizer;

use Paysera\Component\Serializer\Entity\Filter;
use Paysera\Component\Serializer\Entity\NormalizationContext;
use Paysera\Component\Serializer\Entity\Result;
use Paysera\Component\Serializer\Factory\ContextAwareNormalizerFactory;
use Paysera\Component\Serializer\Filter\FieldsFilter;
use Paysera\Component\Serializer\Filter\FieldsParser;
use Paysera\Component\Serializer\Normalizer\PlainItemNormalizer;
use Paysera\Component\Serializer\Normalizer\PlainNormalizer;
use Paysera\Component\Serializer\Normalizer\ResultMetadataNormalizer;
use Paysera\Component\Serializer\Normalizer\ResultNormalizer;
use PHPUnit\Framework\TestCase;

class ResultNormalizerTest extends TestCase
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

    public function testMapToEntityMapsItemsWithItemNormalizer()
    {
        $this->assertEquals(
            (new Result())->setFilter(new Filter())->setItems([1, 2]),
            (new ResultNormalizer('payments', new PlainItemNormalizer('id')))->mapToEntity([
                'payments' => [['id' => 1], ['id' => 2]],
            ])
        );
    }

    /**
     * @dataProvider mapFromEntityProvider
     */
    public function testMapFromEntity($itemNormalizer, array $arguments, array $expected)
    {
        $normalizer = new ResultNormalizer('payments', $itemNormalizer);

        $this->assertSame($normalizer, $normalizer->setMetadataNormalizer(new ResultMetadataNormalizer()));
        $this->assertSame($expected, $normalizer->mapFromEntity(...$arguments));
    }

    public static function mapFromEntityProvider()
    {
        $fieldsParser = new FieldsParser();

        return [
            'items and metadata' => [
                new PlainNormalizer(),
                [(new Result((new Filter())->setLimit(2)))->setTotalCount(5)->setItems([['id' => 1], ['id' => 2]])],
                [
                    'payments' => [['id' => 1], ['id' => 2]],
                    '_metadata' => ['total' => 5, 'limit' => 2, 'offset' => 0],
                ],
            ],
            'items in a context scoped to the items key, metadata not filtered' => [
                (new ContextAwareNormalizerFactory($fieldsParser, new FieldsFilter($fieldsParser)))
                    ->create(new PlainNormalizer()),
                [
                    (new Result(new Filter()))
                        ->setTotalCount(2)
                        ->setItems([['id' => 1, 'name' => 'a'], ['id' => 2, 'name' => 'b']]),
                    (new NormalizationContext())->setFields(['payments.id']),
                ],
                [
                    'payments' => [['id' => 1], ['id' => 2]],
                    '_metadata' => ['total' => 2, 'limit' => null, 'offset' => 0],
                ],
            ],
        ];
    }
}
