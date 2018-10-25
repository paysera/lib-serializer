<?php

namespace Paysera\Component\Serializer\Normalizer;

use Paysera\Component\Serializer\Entity\NormalizationContextInterface;

interface ContextAwareDenormalizerInterface extends DenormalizerInterface
{
    /**
     * Maps raw data to structure.
     *
     * @param $data
     * @param NormalizationContextInterface|null $context
     *
     * @return mixed
     */
    public function mapToEntity($data, NormalizationContextInterface $context = null);
}
