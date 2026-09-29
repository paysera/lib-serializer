<?php

namespace Paysera\Component\Serializer\Tests\Normalizer;

use Paysera\Component\Serializer\Normalizer\DenormalizerInterface;
use Paysera\Component\Serializer\Normalizer\TransformerDenormalizer;
use Paysera\Component\Serializer\Transformer\TransformerInterface;
use PHPUnit\Framework\TestCase;

class TransformerDenormalizerTest extends TestCase
{
    public function testMapToEntityDenormalizesBeforeTransforming()
    {
        $denormalizer = $this->createMock(DenormalizerInterface::class);
        $denormalizer->expects($this->once())->method('mapToEntity')->with(['data'])->willReturn('entity');

        $transformer = $this->createMock(TransformerInterface::class);
        $transformer->expects($this->once())->method('transform')->with('entity')->willReturn('transformed');

        $this->assertSame(
            'transformed',
            (new TransformerDenormalizer($transformer, $denormalizer))->mapToEntity(['data'])
        );
    }
}
