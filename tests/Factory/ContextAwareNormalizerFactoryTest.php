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

        $this->assertEquals(
            new DistributedNormalizer($factory, $fieldsParser, $fieldsFilter, $normalizer),
            $factory->create($normalizer)
        );
    }
}
