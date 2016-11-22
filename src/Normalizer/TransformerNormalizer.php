<?php

namespace Paysera\Component\Serializer\Normalizer;

use Paysera\Component\Serializer\Transformer\TransformerInterface;

class TransformerNormalizer implements NormalizerInterface
{
    /**
     * @var TransformerInterface
     */
    protected $transformer;

    /**
     * @var NormalizerInterface
     */
    protected $normalizer;

    /**
     * @param TransformerInterface  $transformer
     * @param NormalizerInterface   $normalizer
     */
    public function __construct(TransformerInterface $transformer, NormalizerInterface $normalizer)
    {
        $this->transformer = $transformer;
        $this->normalizer = $normalizer;
    }

    /**
     * Maps some structure to raw data. Usually entity object to array
     *
     * @param mixed $entity
     *
     * @return mixed
     */
    public function mapFromEntity($entity)
    {
        return $this->normalizer->mapFromEntity(
            $this->transformer->transform($entity)
        );
    }
}
