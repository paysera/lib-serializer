<?php

namespace Paysera\Component\Serializer\Tests\Normalizer;

use Paysera\Component\Serializer\Normalizer\PlainNormalizer;
use PHPUnit\Framework\TestCase;
use stdClass;

class PlainNormalizerTest extends TestCase
{
    public function testMapFromEntityReturnsEntityUnchanged()
    {
        $entity = new stdClass();

        $this->assertSame($entity, (new PlainNormalizer())->mapFromEntity($entity));
        $this->assertSame(['a' => 1], (new PlainNormalizer())->mapFromEntity(['a' => 1]));
    }

    public function testMapToEntityReturnsDataUnchanged()
    {
        $this->assertSame(['a' => 1], (new PlainNormalizer())->mapToEntity(['a' => 1]));
        $this->assertNull((new PlainNormalizer())->mapToEntity(null));
    }
}
