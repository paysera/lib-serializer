<?php

namespace Paysera\Component\Serializer\Tests\Filter;

use InvalidArgumentException;
use Paysera\Component\Serializer\Filter\FieldsParser;
use PHPUnit\Framework\TestCase;

class FieldsParserTest extends TestCase
{
    public function testNullFieldsIncludeDefaults()
    {
        $config = (new FieldsParser())->parseFields(null);

        $this->assertTrue($config->areDefaultsIncluded());
        $this->assertSame(['*'], $config->getFieldExtensions('any'));
        $this->assertFalse($config->isIncluded('any', false));
    }

    public function testParseFieldsWithoutArgumentsIncludesDefaults()
    {
        $this->assertTrue((new FieldsParser())->parseFields()->areDefaultsIncluded());
    }

    public function testParseUnscopedFieldsCollectsNamesAndExtensions()
    {
        $config = (new FieldsParser())->parseUnscopedFields(['id', 'owner.name,owner.address.city', 'items']);

        $this->assertFalse($config->areDefaultsIncluded());
        $this->assertTrue($config->isIncluded('id'));
        $this->assertTrue($config->isIncluded('owner'));
        $this->assertTrue($config->isIncluded('items'));
        $this->assertFalse($config->isIncluded('name'));
        $this->assertSame(['*'], $config->getFieldExtensions('id'));
        $this->assertSame(['name', 'address.city'], $config->getFieldExtensions('owner'));
    }

    public function testWildcardIncludesDefaults()
    {
        $config = (new FieldsParser())->parseUnscopedFields(['*', 'extra']);

        $this->assertTrue($config->areDefaultsIncluded());
        $this->assertTrue($config->isIncluded('extra', false));
    }

    public function testFieldEndingWithDotThrows()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid field provided, field cannot end with a dot');

        (new FieldsParser())->parseUnscopedFields(['id', 'owner.']);
    }

    public function testScopeDescendsIntoFieldExtensions()
    {
        $config = (new FieldsParser())->parseFields(['owner.address.city', 'id'], ['owner', 'address']);

        $this->assertFalse($config->areDefaultsIncluded());
        $this->assertTrue($config->isIncluded('city'));
        $this->assertFalse($config->isIncluded('id'));
    }

    public function testScopeIntoFieldThatWasNotRequestedIncludesNothing()
    {
        $config = (new FieldsParser())->parseFields(['id'], ['owner']);

        $this->assertFalse($config->areDefaultsIncluded());
        $this->assertFalse($config->isIncluded('id'));
    }

    public function testScopeKeepsDefaultsWhenFieldsAreNull()
    {
        $this->assertTrue((new FieldsParser())->parseFields(null, ['owner'])->areDefaultsIncluded());
    }
}
