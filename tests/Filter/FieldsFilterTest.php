<?php

namespace Paysera\Component\Serializer\Tests\Filter;

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
        $simple = array(
            'key1' => 'value1',
            'key2' => 'value2',
        );
        $complex = array(
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => array('value1', 'value2'),
            'key4' => array('key1' => 'value1', 'key2' => 'value2'),
        );

        return array(
            'Matches everything if * provided' => array(
                'data' => $simple,
                'fields' => array('*'),
                'result' => $simple,
            ),
            'Filters first level data' => array(
                'data' => $complex,
                'fields' => array('key1', 'key4', 'key3'),
                'result' => array(
                    'key1' => 'value1',
                    'key3' => array('value1', 'value2'),
                    'key4' => array('key1' => 'value1', 'key2' => 'value2'),
                ),
            ),
            'Ignores additional fields if * provided' => array(
                'data' => $complex,
                'fields' => array('key1', '*', 'key2'),
                'result' => $complex,
            ),
            'Filters second level data' => array(
                'data' => $complex,
                'fields' => array('key1', 'key3', 'key4.key1'),
                'result' => array(
                    'key1' => 'value1',
                    'key3' => array('value1', 'value2'),
                    'key4' => array('key1' => 'value1'),
                ),
            ),
            'Takes subtree if item provided' => array(
                'data' => $complex,
                'fields' => array('key1', 'key3', 'key4'),
                'result' => array(
                    'key1' => 'value1',
                    'key3' => array('value1', 'value2'),
                    'key4' => array('key1' => 'value1', 'key2' => 'value2'),
                ),
            ),
            'Takes subtree if item with * provided' => array(
                'data' => $complex,
                'fields' => array('key1', 'key3', 'key4.*', 'key.key2'),
                'result' => array(
                    'key1' => 'value1',
                    'key3' => array('value1', 'value2'),
                    'key4' => array('key1' => 'value1', 'key2' => 'value2'),
                ),
            ),
            'Ignores additional fields' => array(
                'data' => $complex,
                'fields' => array('key1', 'key3', 'key4.*', 'key.key2', 'newkey', 'newkey.a', 'newkey.*', 'key4.new'),
                'result' => array(
                    'key1' => 'value1',
                    'key3' => array('value1', 'value2'),
                    'key4' => array('key1' => 'value1', 'key2' => 'value2'),
                ),
            ),
            'Do not take with key 0' => array(
                'data' => array('payments' => array(
                    '0' => array('id' => 123, 'description' => 'abc1'),
                    'asd' => array('id' => 124, 'description' => 'abc2'),
                    'b' => array('id' => 125, 'description' => 'abc3'),
                )),
                'fields' => array('payments.asd'),
                'result' => array('payments' => array(
                    'asd' => array('id' => 124, 'description' => 'abc2'),
                )),
            ),
            'Takes numeric keys' => array(
                'data' => array('payments' => array(
                    '1' => array('id' => 123, 'description' => 'abc1'),
                    'asd' => array('id' => 124, 'description' => 'abc2'),
                    'b' => array('id' => 125, 'description' => 'abc3'),
                )),
                'fields' => array('payments.1'),
                'result' => array('payments' => array(
                    '1' => array('id' => 123, 'description' => 'abc1'),
                )),
            ),
            'Filters for array items' => array(
                'data' => array('payments' => array(
                    array('id' => 123, 'description' => 'abc1'),
                    array('id' => 124, 'description' => 'abc2'),
                    array('id' => 125, 'description' => 'abc3'),
                )),
                'fields' => array('payments.id'),
                'result' => array('payments' => array(
                    array('id' => 123),
                    array('id' => 124),
                    array('id' => 125),
                )),
            ),
            'Filters for array items at top level' => array(
                'data' => array(
                    array('id' => 123, 'description' => 'abc1'),
                    array('id' => 124, 'description' => 'abc2'),
                    array('id' => 125, 'description' => 'abc3'),
                ),
                'fields' => array('id'),
                'result' => array(
                    array('id' => 123),
                    array('id' => 124),
                    array('id' => 125),
                ),
            ),
            'Correctly gets associative arrays' => array(
                'data' => array('payments' => array(
                    array('id' => 123, 'description' => 'abc1'),
                    array('id' => 124, 'description' => 'abc2'),
                    5 => array('id' => 125, 'description' => 'abc3'),
                )),
                'fields' => array('payments.id'),
                'result' => array('payments' => new \ArrayObject()),
            ),
            'Takes all fields if wildcard on parent specified' => array(
                'data' => array('a1' => array('a2' => array('a3' => array('a4' => 'value1', 'a5' => 'value2')))),
                'fields' => array('*', 'a1.a2.a3.a4'),
                'result' => array('a1' => array('a2' => array('a3' => array('a4' => 'value1', 'a5' => 'value2')))),
            ),
            'Correctly filters deep-nested arrays' => array(
                'data' => array('a1' => array(
                    'a2' => array('a3' => array('a4' => 'value1', 'a5' => 'value2'), 'a32' => '1'),
                )),
                'fields' => array('a1.a2.a3.a4'),
                'result' => array('a1' => array('a2' => array('a3' => array('a4' => 'value1')))),
            ),
            'Takes keys from second level arrays' => array(
                'data' => array('scalar' => 'asd', 'array' => array(
                    array('item1' => 'asd', 'item2' => 'qwe', 'item3' => array('a', 'b')),
                    array('item1' => 'qwe', 'item2' => 'rty', 'item3' => array('c', 'd')),
                    array('item1' => 'fgh', 'item2' => 'yui', 'item3' => array('e', 'f')),
                )),
                'fields' => array('array.item1', 'array.item3'),
                'result' => array('array' => array(
                    array('item1' => 'asd', 'item3' => array('a', 'b')),
                    array('item1' => 'qwe', 'item3' => array('c', 'd')),
                    array('item1' => 'fgh', 'item3' => array('e', 'f')),
                )),
            ),
            'Takes several fields from one item' => array(
                'data' => array('a1' => '1', 'a2' => '2', 'a3' => '3', 'a4' => '4'),
                'fields' => array('a1,a4', 'a2'),
                'result' => array('a1' => '1', 'a2' => '2', 'a4' => '4'),
            ),
            'Leaves curly braces if all items are filtered' => array(
                'data' => array('a1' => '1', 'a2' => '2', 'a3' => '3', 'a4' => '4'),
                'fields' => array('b1'),
                'result' => new \ArrayObject(),
            ),
            'Leaves simple array if all items are filtered' => array(
                'data' => array('a1' => array('a', 'b', 'c')),
                'fields' => array('a1.b1'),
                'result' => array('a1' => array('a', 'b', 'c')),
            ),
            // todo:
//            'Takes curly braces' => array(
//                'data' => array('a1' => '1', 'a2' => '2', 'a3' => array(
//                    'a31' => '31',
//                    'a32' => '32',
//                    'a33' => array(
//                        array('a331' => '331a', 'a332' => '332a', 'a333' => '333a', 'a334' => '334a'),
//                        array('a331' => '331b', 'a332' => '332b', 'a333' => '333b', 'a334' => '334a'),
//                        array('a331' => '331c', 'a332' => '332c', 'a333' => '333c', 'a334' => '334a'),
//                    ),
//                ), 'a4' => '4'),
//                'fields' => array('a3.{a31,a33.a331,a33.{a333}}', '{a1,a4},a3.a33.a334'),
//                'result' => array('a1' => '1', 'a3' => array(
//                    'a31' => '31',
//                    'a33' => array(
//                        array('a331' => '331a', 'a333' => '333a', 'a334' => '334a'),
//                        array('a331' => '331b', 'a333' => '333b', 'a334' => '334a'),
//                        array('a331' => '331c', 'a333' => '333c', 'a334' => '334a'),
//                    ),
//                ), 'a4' => '4'),
//            ),
        );
    }

    public function filterWithScopeProvider()
    {
        $simple = array(
            'key1' => 'value1',
            'key2' => 'value2',
        );
        $complex = array(
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => array('value1', 'value2'),
            'key4' => array('key1' => 'value1', 'key2' => 'value2'),
        );

        return array(
            'Matches everything if * provided' => array(
                'data' => $simple,
                'fields' => array('*'),
                'scope' => array('scope'),
                'result' => $simple,
            ),
            'Filters second level data' => array(
                'data' => $complex,
                'fields' => array('key0.key1', 'key0.key3', 'key0.key4.key1'),
                'scope' => array('key0'),
                'result' => array(
                    'key1' => 'value1',
                    'key3' => array('value1', 'value2'),
                    'key4' => array('key1' => 'value1'),
                ),
            ),
            'Correctly gets associative arrays' => array(
                'data' => array('payments' => array(
                    array('id' => 123, 'description' => 'abc1'),
                    array('id' => 124, 'description' => 'abc2'),
                    5 => array('id' => 125, 'description' => 'abc3'),
                )),
                'fields' => array('scope.payments.id'),
                'scope' => array('scope'),
                'result' => array('payments' => new \ArrayObject()),
            ),
            'Takes all fields if wildcard on parent specified' => array(
                'data' => array('a1' => array('a2' => array('a3' => array('a4' => 'value1', 'a5' => 'value2')))),
                'fields' => array('*', 'scope.a1.a2.a3.a4'),
                'scope' => array('scope'),
                'result' => array('a1' => array('a2' => array('a3' => array('a4' => 'value1', 'a5' => 'value2')))),
            ),
            'Ignores other fields' => array(
                'data' => array('a1' => array('a2' => array('a3' => array('a4' => 'value1', 'a5' => 'value2')))),
                'fields' => array('a0.a1.a2.a3.a4', 'aa.a1.a2.a3.a5'),
                'scope' => array('a0'),
                'result' => array('a1' => array('a2' => array('a3' => array('a4' => 'value1')))),
            ),
            'Takes nested scope' => array(
                'data' => array(array('a4' => 'value1', 'a5' => 'value2')),
                'fields' => array('a0.a1.a2.a3.a4', 'aa.a1.a2.a3.a5'),
                'scope' => array('a0', 'a1', 'a2', 'a3'),
                'result' => array(array('a4' => 'value1')),
            ),
            'Filters if on another branch' => array(
                'data' => array(array('a4' => 'value1', 'a5' => 'value2')),
                'fields' => array('a1.a2'),
                'scope' => array('a2'),
                'result' => array(array()),
            ),
        );
    }
}
