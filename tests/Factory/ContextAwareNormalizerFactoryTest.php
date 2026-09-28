<?php

namespace Paysera\Component\Serializer\Tests\Factory;

use Paysera\Component\Serializer\Factory\ContextAwareNormalizerFactory;
use Paysera\Component\Serializer\Filter\FieldsFilter;
use Paysera\Component\Serializer\Filter\FieldsParser;
use Paysera\Component\Serializer\Normalizer\DistributedNormalizer;
use Paysera\Component\Serializer\Normalizer\PlainNormalizer;
use PHPUnit\Framework\TestCase;

class ContextAwareNormalizerFactoryTest extends TestCase
{
    public function testCreateWrapsNormalizerInDistributedNormalizer()
    {
        $fieldsParser = new FieldsParser();
        $fieldsFilter = new FieldsFilter($fieldsParser);
        $factory = new ContextAwareNormalizerFactory($fieldsParser, $fieldsFilter);
        $normalizer = new PlainNormalizer();

        $created = $factory->create($normalizer);

        $this->assertEquals(new DistributedNormalizer($factory, $fieldsParser, $fieldsFilter, $normalizer), $created);
        $this->assertSame(
            [$factory, $fieldsParser, $fieldsFilter, $normalizer],
            (function () {
                return [$this->factory, $this->fieldsParser, $this->fieldsFilter, $this->normalizer];
            })->call($created)
        );
    }
}
