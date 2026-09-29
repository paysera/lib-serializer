<?php

namespace Paysera\Component\Serializer\Tests\Factory;

use Paysera\Component\Serializer\Factory\ContextAwareNormalizerFactory;
use Paysera\Component\Serializer\Filter\FieldsFilter;
use Paysera\Component\Serializer\Filter\FieldsParser;
use Paysera\Component\Serializer\Normalizer\DistributedNormalizer;
use Paysera\Component\Serializer\Normalizer\PlainNormalizer;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

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
            array_map(function ($property) use ($created) {
                $reflectionProperty = new ReflectionProperty(DistributedNormalizer::class, $property);
                if (PHP_VERSION_ID < 80100) {
                    $reflectionProperty->setAccessible(true);
                }

                return $reflectionProperty->getValue($created);
            }, ['factory', 'fieldsParser', 'fieldsFilter', 'normalizer'])
        );
    }
}
