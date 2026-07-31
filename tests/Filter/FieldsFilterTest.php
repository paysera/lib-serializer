<?php

namespace Paysera\Component\Serializer\Tests\Filter;

use ArrayObject;
use Paysera\Component\Serializer\Filter\FieldsFilter;
use Paysera\Component\Serializer\Filter\FieldsParser;
use PHPUnit\Framework\TestCase;

class FieldsFilterTest extends TestCase
{
    /**
     * @var FieldsFilter
     */
    protected $fieldsFilter;

    /**
     * @var FieldsParser
     */
    protected $fieldsParser;

    protected function setUp(): void
    {
        $this->fieldsFilter = new FieldsFilter(new FieldsParser());
    }


    /**
     * @param array $data
     * @param array $fields
     * @param array $result
     *
     * @dataProvider filterProvider
     */
    public function testFilter($data, $fields, $result)
    {
        $this->assertEquals($result, $this->fieldsFilter->filter($data, $fields));
    }

    /**
     * @param array $data
     * @param array $fields
     * @param array $scope
     * @param array $result
     *
     * @dataProvider filterWithScopeProvider
     */
    public function testFilterWithScope($data, $fields, $scope, $result)
    {
        $this->assertEquals($result, $this->fieldsFilter->filter($data, $fields, $scope));
    }

    public function filterProvider()
    {
        $simple = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];
        $complex = [
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => ['value1', 'value2'],
            'key4' => ['key1' => 'value1', 'key2' => 'value2'],
        ];

        return [
            'Matches everything if * provided' => [
                'data' => $simple,
                'fields' => ['*'],
                'result' => $simple,
            ],
            'Filters first level data' => [
                'data' => $complex,
                'fields' => ['key1', 'key4', 'key3'],
                'result' => [
                    'key1' => 'value1',
                    'key3' => ['value1', 'value2'],
                    'key4' => ['key1' => 'value1', 'key2' => 'value2'],
                ],
            ],
            'Ignores additional fields if * provided' => [
                'data' => $complex,
                'fields' => ['key1', '*', 'key2'],
                'result' => $complex,
            ],
            'Filters second level data' => [
                'data' => $complex,
                'fields' => ['key1', 'key3', 'key4.key1'],
                'result' => [
                    'key1' => 'value1',
                    'key3' => ['value1', 'value2'],
                    'key4' => ['key1' => 'value1'],
                ],
            ],
            'Takes subtree if item provided' => [
                'data' => $complex,
                'fields' => ['key1', 'key3', 'key4'],
                'result' => [
                    'key1' => 'value1',
                    'key3' => ['value1', 'value2'],
                    'key4' => ['key1' => 'value1', 'key2' => 'value2'],
                ],
            ],
            'Takes subtree if item with * provided' => [
                'data' => $complex,
                'fields' => ['key1', 'key3', 'key4.*', 'key.key2'],
                'result' => [
                    'key1' => 'value1',
                    'key3' => ['value1', 'value2'],
                    'key4' => ['key1' => 'value1', 'key2' => 'value2'],
                ],
            ],
            'Ignores additional fields' => [
                'data' => $complex,
                'fields' => ['key1', 'key3', 'key4.*', 'key.key2', 'newkey', 'newkey.a', 'newkey.*', 'key4.new'],
                'result' => [
                    'key1' => 'value1',
                    'key3' => ['value1', 'value2'],
                    'key4' => ['key1' => 'value1', 'key2' => 'value2'],
                ],
            ],
            'Do not take with key 0' => [
                'data' => ['payments' => [
                    '0' => ['id' => 123, 'description' => 'abc1'],
                    'asd' => ['id' => 124, 'description' => 'abc2'],
                    'b' => ['id' => 125, 'description' => 'abc3'],
                ]],
                'fields' => ['payments.asd'],
                'result' => ['payments' => [
                    'asd' => ['id' => 124, 'description' => 'abc2'],
                ]],
            ],
            'Takes numeric keys' => [
                'data' => ['payments' => [
                    '1' => ['id' => 123, 'description' => 'abc1'],
                    'asd' => ['id' => 124, 'description' => 'abc2'],
                    'b' => ['id' => 125, 'description' => 'abc3'],
                ]],
                'fields' => ['payments.1'],
                'result' => ['payments' => [
                    '1' => ['id' => 123, 'description' => 'abc1'],
                ]],
            ],
            'Filters for array items' => [
                'data' => ['payments' => [
                    ['id' => 123, 'description' => 'abc1'],
                    ['id' => 124, 'description' => 'abc2'],
                    ['id' => 125, 'description' => 'abc3'],
                ]],
                'fields' => ['payments.id'],
                'result' => ['payments' => [
                    ['id' => 123],
                    ['id' => 124],
                    ['id' => 125],
                ]],
            ],
            'Filters for array items at top level' => [
                'data' => [
                    ['id' => 123, 'description' => 'abc1'],
                    ['id' => 124, 'description' => 'abc2'],
                    ['id' => 125, 'description' => 'abc3'],
                ],
                'fields' => ['id'],
                'result' => [
                    ['id' => 123],
                    ['id' => 124],
                    ['id' => 125],
                ],
            ],
            'Correctly gets associative arrays' => [
                'data' => ['payments' => [
                    ['id' => 123, 'description' => 'abc1'],
                    ['id' => 124, 'description' => 'abc2'],
                    5 => ['id' => 125, 'description' => 'abc3'],
                ]],
                'fields' => ['payments.id'],
                'result' => ['payments' => new ArrayObject()],
            ],
            'Takes all fields if wildcard on parent specified' => [
                'data' => ['a1' => ['a2' => ['a3' => ['a4' => 'value1', 'a5' => 'value2']]]],
                'fields' => ['*', 'a1.a2.a3.a4'],
                'result' => ['a1' => ['a2' => ['a3' => ['a4' => 'value1', 'a5' => 'value2']]]],
            ],
            'Correctly filters deep-nested arrays' => [
                'data' => ['a1' => [
                    'a2' => ['a3' => ['a4' => 'value1', 'a5' => 'value2'], 'a32' => '1'],
                ]],
                'fields' => ['a1.a2.a3.a4'],
                'result' => ['a1' => ['a2' => ['a3' => ['a4' => 'value1']]]],
            ],
            'Takes keys from second level arrays' => [
                'data' => ['scalar' => 'asd', 'array' => [
                    ['item1' => 'asd', 'item2' => 'qwe', 'item3' => ['a', 'b']],
                    ['item1' => 'qwe', 'item2' => 'rty', 'item3' => ['c', 'd']],
                    ['item1' => 'fgh', 'item2' => 'yui', 'item3' => ['e', 'f']],
                ]],
                'fields' => ['array.item1', 'array.item3'],
                'result' => ['array' => [
                    ['item1' => 'asd', 'item3' => ['a', 'b']],
                    ['item1' => 'qwe', 'item3' => ['c', 'd']],
                    ['item1' => 'fgh', 'item3' => ['e', 'f']],
                ]],
            ],
            'Takes several fields from one item' => [
                'data' => ['a1' => '1', 'a2' => '2', 'a3' => '3', 'a4' => '4'],
                'fields' => ['a1,a4', 'a2'],
                'result' => ['a1' => '1', 'a2' => '2', 'a4' => '4'],
            ],
            'Leaves curly braces if all items are filtered' => [
                'data' => ['a1' => '1', 'a2' => '2', 'a3' => '3', 'a4' => '4'],
                'fields' => ['b1'],
                'result' => new ArrayObject(),
            ],
            'Leaves simple array if all items are filtered' => [
                'data' => ['a1' => ['a', 'b', 'c']],
                'fields' => ['a1.b1'],
                'result' => ['a1' => ['a', 'b', 'c']],
            ],
            // todo:
//            'Takes curly braces' => [
//                'data' => ['a1' => '1', 'a2' => '2', 'a3' => [
//                    'a31' => '31',
//                    'a32' => '32',
//                    'a33' => [
//                        ['a331' => '331a', 'a332' => '332a', 'a333' => '333a', 'a334' => '334a'],
//                        ['a331' => '331b', 'a332' => '332b', 'a333' => '333b', 'a334' => '334a'],
//                        ['a331' => '331c', 'a332' => '332c', 'a333' => '333c', 'a334' => '334a'],
//                    ],
//                ], 'a4' => '4'],
//                'fields' => ['a3.{a31,a33.a331,a33.{a333}}', '{a1,a4},a3.a33.a334'],
//                'result' => ['a1' => '1', 'a3' => [
//                    'a31' => '31',
//                    'a33' => [
//                        ['a331' => '331a', 'a333' => '333a', 'a334' => '334a'],
//                        ['a331' => '331b', 'a333' => '333b', 'a334' => '334a'],
//                        ['a331' => '331c', 'a333' => '333c', 'a334' => '334a'],
//                    ],
//                ], 'a4' => '4'],
//            ],
        ];
    }

    public function filterWithScopeProvider()
    {
        $simple = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];
        $complex = [
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => ['value1', 'value2'],
            'key4' => ['key1' => 'value1', 'key2' => 'value2'],
        ];

        return [
            'Matches everything if * provided' => [
                'data' => $simple,
                'fields' => ['*'],
                'scope' => ['scope'],
                'result' => $simple,
            ],
            'Filters second level data' => [
                'data' => $complex,
                'fields' => ['key0.key1', 'key0.key3', 'key0.key4.key1'],
                'scope' => ['key0'],
                'result' => [
                    'key1' => 'value1',
                    'key3' => ['value1', 'value2'],
                    'key4' => ['key1' => 'value1'],
                ],
            ],
            'Correctly gets associative arrays' => [
                'data' => ['payments' => [
                    ['id' => 123, 'description' => 'abc1'],
                    ['id' => 124, 'description' => 'abc2'],
                    5 => ['id' => 125, 'description' => 'abc3'],
                ]],
                'fields' => ['scope.payments.id'],
                'scope' => ['scope'],
                'result' => ['payments' => new ArrayObject()],
            ],
            'Takes all fields if wildcard on parent specified' => [
                'data' => ['a1' => ['a2' => ['a3' => ['a4' => 'value1', 'a5' => 'value2']]]],
                'fields' => ['*', 'scope.a1.a2.a3.a4'],
                'scope' => ['scope'],
                'result' => ['a1' => ['a2' => ['a3' => ['a4' => 'value1', 'a5' => 'value2']]]],
            ],
            'Ignores other fields' => [
                'data' => ['a1' => ['a2' => ['a3' => ['a4' => 'value1', 'a5' => 'value2']]]],
                'fields' => ['a0.a1.a2.a3.a4', 'aa.a1.a2.a3.a5'],
                'scope' => ['a0'],
                'result' => ['a1' => ['a2' => ['a3' => ['a4' => 'value1']]]],
            ],
            'Takes nested scope' => [
                'data' => [['a4' => 'value1', 'a5' => 'value2']],
                'fields' => ['a0.a1.a2.a3.a4', 'aa.a1.a2.a3.a5'],
                'scope' => ['a0', 'a1', 'a2', 'a3'],
                'result' => [['a4' => 'value1']],
            ],
            'Filters if on another branch' => [
                'data' => [['a4' => 'value1', 'a5' => 'value2']],
                'fields' => ['a1.a2'],
                'scope' => ['a2'],
                'result' => [[]],
            ],
        ];
    }
}
