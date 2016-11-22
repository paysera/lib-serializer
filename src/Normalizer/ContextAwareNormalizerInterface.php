<?php

namespace Paysera\Component\Serializer\Normalizer;

use Paysera\Component\Serializer\Entity\NormalizationContextInterface;

interface ContextAwareNormalizerInterface extends NormalizerInterface
{
    /**
     * Maps some structure to raw data. Usually entity object to array
     *
     * @param mixed                                                          $entity
     * @param \Paysera\Component\Serializer\Entity\NormalizationContextInterface $context
     *
     * @return mixed
     */
    public function mapFromEntity($entity, NormalizationContextInterface $context = null);

}
