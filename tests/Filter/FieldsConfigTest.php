<?php

namespace Paysera\Component\Serializer\Tests\Filter;

use Paysera\Component\Serializer\Filter\FieldsConfig;
use PHPUnit\Framework\TestCase;

class FieldsConfigTest extends TestCase
{
    /**
     * @dataProvider isIncludedProvider
     */
    public function testIsIncluded(FieldsConfig $config, array $arguments, $expected)
    {
        $this->assertSame($expected, $config->isIncluded(...$arguments));
    }

    public static function isIncludedProvider()
    {
        $withDefaults = new FieldsConfig(true, ['extra'], []);
        $withoutDefaults = new FieldsConfig(false, ['id', '0'], []);

        return [
            'default field with defaults included' => [new FieldsConfig(true, [], []), ['anything'], true],
            'listed non-default field' => [$withDefaults, ['extra', false], true],
            'unlisted non-default field' => [$withDefaults, ['other', false], false],
            'listed field without defaults' => [$withoutDefaults, ['id'], true],
            'integer name of a listed field' => [$withoutDefaults, [0], true],
            'unlisted field without defaults' => [$withoutDefaults, ['name'], false],
        ];
    }

    /**
     * @dataProvider defaultsIncludedProvider
     */
    public function testAreDefaultsIncluded($defaultsIncluded, $expected)
    {
        $this->assertSame($expected, (new FieldsConfig($defaultsIncluded, [], []))->areDefaultsIncluded());
    }

    public static function defaultsIncludedProvider()
    {
        return [
            'true' => [true, true],
            'false' => [false, false],
            'integer one' => [1, true],
            'null' => [null, false],
        ];
    }

    /**
     * @dataProvider fieldExtensionsProvider
     */
    public function testGetFieldExtensions($defaultsIncluded, $fieldName, array $expected)
    {
        $config = new FieldsConfig($defaultsIncluded, ['owner'], ['owner' => ['name', 'email']]);

        $this->assertSame($expected, $config->getFieldExtensions($fieldName));
    }

    public static function fieldExtensionsProvider()
    {
        return [
            'listed field without defaults' => [false, 'owner', ['name', 'email']],
            'listed field with defaults' => [true, 'owner', ['name', 'email', '*']],
            'unlisted field without defaults' => [false, 'items', []],
            'unlisted field with defaults' => [true, 'items', ['*']],
        ];
    }
}
