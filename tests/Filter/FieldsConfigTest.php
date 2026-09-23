<?php

namespace Paysera\Component\Serializer\Tests\Filter;

use Paysera\Component\Serializer\Filter\FieldsConfig;
use PHPUnit\Framework\TestCase;

class FieldsConfigTest extends TestCase
{
    public function testDefaultFieldsAreIncludedWhenDefaultsAreIncluded()
    {
        $config = new FieldsConfig(true, [], []);

        $this->assertTrue($config->areDefaultsIncluded());
        $this->assertTrue($config->isIncluded('anything'));
    }

    public function testNonDefaultFieldIsIncludedOnlyWhenListed()
    {
        $config = new FieldsConfig(true, ['extra'], []);

        $this->assertTrue($config->isIncluded('extra', false));
        $this->assertFalse($config->isIncluded('other', false));
    }

    public function testOnlyListedFieldsAreIncludedWithoutDefaults()
    {
        $config = new FieldsConfig(false, ['id', '0'], []);

        $this->assertFalse($config->areDefaultsIncluded());
        $this->assertTrue($config->isIncluded('id'));
        $this->assertTrue($config->isIncluded(0));
        $this->assertFalse($config->isIncluded('name'));
    }

    public function testDefaultsIncludedFlagIsCastToBoolean()
    {
        $this->assertTrue((new FieldsConfig(1, [], []))->areDefaultsIncluded());
        $this->assertFalse((new FieldsConfig(null, [], []))->areDefaultsIncluded());
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
