<?php

namespace Paysera\Component\Serializer\Tests\Factory;

use Paysera\Component\Serializer\Entity\NormalizationContext;
use Paysera\Component\Serializer\Factory\ContextAwareNormalizerFactory;
use Paysera\Component\Serializer\Filter\FieldsFilter;
use Paysera\Component\Serializer\Filter\FieldsParser;
use Paysera\Component\Serializer\Normalizer\DistributedNormalizer;
use Paysera\Component\Serializer\Normalizer\PlainNormalizer;
use PHPUnit\Framework\TestCase;

class ContextAwareNormalizerFactoryTest extends TestCase
{
    public function testCreateWrapsNormalizerSoOutputIsFilteredByContextFields()
    {
        $fieldsParser = new FieldsParser();
        $factory = new ContextAwareNormalizerFactory($fieldsParser, new FieldsFilter($fieldsParser));

        $normalizer = $factory->create(new PlainNormalizer());

        $this->assertInstanceOf(DistributedNormalizer::class, $normalizer);
        $this->assertSame(
            ['id' => 1],
            $normalizer->mapFromEntity(['id' => 1, 'name' => 'John'], (new NormalizationContext())->setFields(['id']))
        );
        $this->assertSame(['id' => 1, 'name' => 'John'], $normalizer->mapToEntity(['id' => 1, 'name' => 'John']));
    }
}
