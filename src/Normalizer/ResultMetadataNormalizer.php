<?php

namespace Paysera\Component\Serializer\Normalizer;

use Paysera\Component\Serializer\Entity\Result;

class ResultMetadataNormalizer implements NormalizerInterface
{

    /**
     * Maps some structure to raw data. Usually entity object to array
     *
     * @param Result $result
     *
     * @return array
     */
    public function mapFromEntity($result)
    {
        $filter = $result->getFilter();
        return array(
            'total' => $result->getTotalCount(),
            'offset' => $filter ? $filter->getOffset() : 0,
            'limit' => $filter ? $filter->getLimit() : null,
        );
    }
}
