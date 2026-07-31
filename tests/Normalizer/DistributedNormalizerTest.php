<?php

namespace Paysera\Component\Serializer\Tests\Normalizer;

use Paysera\Component\Serializer\Normalizer\DistributedNormalizer;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;

class DistributedNormalizerTest extends TestCase
{
    /**
     * The three field maps default on the property declarations rather than in the
     * constructor, so they survive every instantiation path. A constructor assignment
     * would leave them null for instances built without one — doubles created with
     * disableOriginalConstructor(), or reflection hydration. Both readers degrade
     * silently on null rather than throwing: mapFromEntity() iterates $fieldAccessors,
     * which warns and skips, dropping every distributed field from the output, and
     * mapToEntity() probes isset($this->fieldNormalizers[$key]), which is simply false
     * for every key.
     *
     * @dataProvider fieldMapProvider
     */
    public function testFieldMapsDefaultToArrayWhenConstructorIsBypassed($property)
    {
        $normalizer = (new ReflectionClass(DistributedNormalizer::class))->newInstanceWithoutConstructor();

        $reflectionProperty = new ReflectionProperty(DistributedNormalizer::class, $property);
        $reflectionProperty->setAccessible(true);

        $this->assertSame([], $reflectionProperty->getValue($normalizer));
    }

    public function fieldMapProvider()
    {
        return [
            'fieldAccessors' => ['fieldAccessors'],
            'fieldDefault' => ['fieldDefault'],
            'fieldNormalizers' => ['fieldNormalizers'],
        ];
    }
}
