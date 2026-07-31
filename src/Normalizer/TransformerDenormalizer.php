<?php

namespace Paysera\Component\Serializer\Normalizer;

use Paysera\Component\Serializer\Exception\InvalidDataException;
use Paysera\Component\Serializer\Transformer\TransformerInterface;

class TransformerDenormalizer implements DenormalizerInterface
{
    /**
     * @var TransformerInterface
     */
    protected $transformer;

    /**
     * @var DenormalizerInterface
     */
    protected $denormalizer;

    /**
     * @param TransformerInterface  $transformer
     * @param DenormalizerInterface $denormalizer
     */
    public function __construct(TransformerInterface $transformer, DenormalizerInterface $denormalizer)
    {
        $this->transformer = $transformer;
        $this->denormalizer = $denormalizer;
    }

    /**
     * Maps raw data to some structure. Usually array to entity object
     *
     * @param mixed $data
     *
     * @return mixed
     *
     * @throws InvalidDataException
     */
    public function mapToEntity($data)
    {
        return $this->transformer->transform(
            $this->denormalizer->mapToEntity($data)
        );
    }
}
