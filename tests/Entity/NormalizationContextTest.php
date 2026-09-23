<?php

namespace Paysera\Component\Serializer\Tests\Entity;

use Paysera\Component\Serializer\Entity\NormalizationContext;
use PHPUnit\Framework\TestCase;

class NormalizationContextTest extends TestCase
{
    public function testFieldsAndScopeDefaultToEmpty()
    {
        $context = new NormalizationContext();

        $this->assertSame([], $context->getFields());
        $this->assertSame([], $context->getScope());
    }

    public function testSetFieldsIsFluent()
    {
        $context = new NormalizationContext();

        $this->assertSame($context, $context->setFields(['id', 'items.name']));
        $this->assertSame(['id', 'items.name'], $context->getFields());
    }

    public function testCreateScopedContextReturnsCopyWithFieldAppendedToScope()
    {
        $context = (new NormalizationContext())->setFields(['items.name']);

        $scoped = $context->createScopedContext('items');
        $nested = $scoped->createScopedContext('owner');

        $this->assertNotSame($context, $scoped);
        $this->assertSame(['items.name'], $scoped->getFields());
        $this->assertSame(['items'], $scoped->getScope());
        $this->assertSame(['items', 'owner'], $nested->getScope());
        $this->assertSame([], $context->getScope());
    }
}
