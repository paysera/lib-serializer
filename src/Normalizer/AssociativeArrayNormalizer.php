<?php

namespace Paysera\Component\Serializer\Normalizer;

class AssociativeArrayNormalizer implements DenormalizerInterface, NormalizerInterface
{
    /**
     * @var NormalizerInterface
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
     * Returns same data
     *
     * @param mixed $entity
     *
     * @return mixed
     */
    public function mapFromEntity($entity)
    {
        $result = new \ArrayObject();
        if ($entity !== null) {
            foreach ($entity as $key => $innerElement) {
                $result[$key] = $this->innerMapper->mapFromEntity($innerElement);
            }
        }
        return $result;
    }

    /**
     * Maps raw data to some structure. Usually array to entity object
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function mapToEntity($data)
    {
        $result = array();
        if ($data !== null) {
            foreach ($data as $key => $innerElement) {
                $result[$key] = $this->innerMapper->mapToEntity($innerElement);
            }
        }

        return $result;
    }
}
