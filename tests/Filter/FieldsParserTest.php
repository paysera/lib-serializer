<?php

namespace Paysera\Component\Serializer\Tests\Filter;

use InvalidArgumentException;
use Paysera\Component\Serializer\Filter\FieldsConfig;
use Paysera\Component\Serializer\Filter\FieldsParser;
use PHPUnit\Framework\TestCase;

class FieldsParserTest extends TestCase
{
    /**
     * @dataProvider unscopedFieldsProvider
     */
    public function testParseUnscopedFields(array $fields, FieldsConfig $expected)
    {
        $this->assertEquals($expected, (new FieldsParser())->parseUnscopedFields($fields));
    }

    public static function unscopedFieldsProvider()
    {
        return [
            'names and extensions' => [
                ['id', 'owner.name,owner.address.city', 'items'],
                new FieldsConfig(
                    false,
                    ['id', 'owner', 'owner', 'items'],
                    ['id' => ['*'], 'owner' => ['name', 'address.city'], 'items' => ['*']]
                ),
            ],
            'wildcard' => [['*', 'extra'], new FieldsConfig(true, ['extra'], ['extra' => ['*']])],
        ];
    }

    /**
     * @dataProvider fieldsProvider
     */
    public function testParseFields(array $arguments, FieldsConfig $expected)
    {
        $this->assertEquals($expected, (new FieldsParser())->parseFields(...$arguments));
    }

    public static function fieldsProvider()
    {
        return [
            'no arguments' => [[], new FieldsConfig(true, [], [])],
            'null fields' => [[null], new FieldsConfig(true, [], [])],
            'null fields with a scope' => [[null, ['owner']], new FieldsConfig(true, [], [])],
            'scope descends into field extensions' => [
                [['owner.address.city', 'id'], ['owner', 'address']],
                new FieldsConfig(false, ['city'], ['city' => ['*']]),
            ],
            'scope into a field that was not requested' => [[['id'], ['owner']], new FieldsConfig(false, [], [])],
            'subfield listed after the wildcard' => [
                [['*', 'owner.secret'], ['owner']],
                new FieldsConfig(true, ['secret'], ['secret' => ['*']]),
            ],
            'subfield listed before the wildcard' => [
                [['owner.secret', '*'], ['owner']],
                new FieldsConfig(true, ['secret'], ['secret' => ['*']]),
            ],
        ];
    }

    public function testFieldEndingWithDotThrows()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid field provided, field cannot end with a dot');

        (new FieldsParser())->parseUnscopedFields(['id', 'owner.']);
    }
}
