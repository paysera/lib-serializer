<?php

namespace Paysera\Component\Serializer\Tests\Entity;

use Paysera\Component\Serializer\Entity\NormalizationContext;
use PHPUnit\Framework\TestCase;

class NormalizationContextTest extends TestCase
{
    /**
     * @dataProvider contextProvider
     */
    public function testFieldsAndScope(NormalizationContext $context, array $expected)
    {
        $this->assertSame($expected, ['fields' => $context->getFields(), 'scope' => $context->getScope()]);
    }

    public static function contextProvider()
    {
        return [
            'new' => [new NormalizationContext(), ['fields' => [], 'scope' => []]],
            'fields set' => [
                (new NormalizationContext())->setFields(['id', 'items.name']),
                ['fields' => ['id', 'items.name'], 'scope' => []],
            ],
            'scoped' => [
                (new NormalizationContext())->setFields(['items.name'])->createScopedContext('items'),
                ['fields' => ['items.name'], 'scope' => ['items']],
            ],
            'scoped twice' => [
                (new NormalizationContext())->setFields(['items.name'])
                    ->createScopedContext('items')
                    ->createScopedContext('owner'),
                ['fields' => ['items.name'], 'scope' => ['items', 'owner']],
            ],
        ];
    }

    public function testSetFieldsIsFluent()
    {
        $context = new NormalizationContext();

        $this->assertSame($context, $context->setFields(['id']));
    }

    public function testCreateScopedContextReturnsCopyAndLeavesOriginalUnchanged()
    {
        $context = (new NormalizationContext())->setFields(['items.name']);

        $this->assertNotSame($context, $context->createScopedContext('items'));
        $this->assertEquals((new NormalizationContext())->setFields(['items.name']), $context);
    }
}
