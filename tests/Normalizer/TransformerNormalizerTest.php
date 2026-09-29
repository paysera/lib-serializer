<?php

namespace Paysera\Component\Serializer\Tests\Normalizer;

use Paysera\Component\Serializer\Normalizer\NormalizerInterface;
use Paysera\Component\Serializer\Normalizer\TransformerNormalizer;
use Paysera\Component\Serializer\Transformer\TransformerInterface;
use PHPUnit\Framework\TestCase;

class TransformerNormalizerTest extends TestCase
{
    public function testMapFromEntityTransformsBeforeNormalizing()
    {
        $transformer = $this->createMock(TransformerInterface::class);
        $transformer->expects($this->once())->method('transform')->with('entity')->willReturn('transformed');

        $normalizer = $this->createMock(NormalizerInterface::class);
        $normalizer->expects($this->once())->method('mapFromEntity')->with('transformed')->willReturn(['data']);

        $this->assertSame(['data'], (new TransformerNormalizer($transformer, $normalizer))->mapFromEntity('entity'));
    }
}
