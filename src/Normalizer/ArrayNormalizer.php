<?php

namespace Paysera\Component\Serializer\Normalizer;

use Paysera\Component\Serializer\Entity\NormalizationContextInterface;

class ArrayNormalizer implements DenormalizerInterface, ContextAwareNormalizerInterface
{
    /**
     * @var DenormalizerInterface|NormalizerInterface
     */
    protected $innerMapper;

    /**
     * Constructs object
     *
     * @param DenormalizerInterface|NormalizerInterface $innerMapper
     */
    public function __construct($innerMapper)
    {
        $this->innerMapper = $innerMapper;
    }

    /**
     * Maps raw data to some structure. Usually array to entity object
     *
     * @param mixed $data
     *
     * @return mixed
     *
     * @throws \Paysera\Component\Serializer\Exception\InvalidDataException
     */
    public function mapToEntity($data)
    {
        $result = array();
        if ($data !== null) {
            foreach ($data as $innerElement) {
                $result[] = $this->innerMapper->mapToEntity($innerElement);
            }
        }
        return $result;
    }

    public function mapFromEntity($entity, NormalizationContextInterface $context = null)
    {
        $result = array();
        if ($entity !== null) {
            foreach ($entity as $innerElement) {
                $result[] = $this->innerMapper->mapFromEntity($innerElement, $context);
            }
        }
        return $result;
    }
}
