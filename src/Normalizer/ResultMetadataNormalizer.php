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

        $data = array(
            'total' => $result->getTotalCount(),
            'limit' => $filter ? $filter->getLimit() : null,
        );

        if ($result->getAfter() !== null) {
            $data['cursors']['after'] = $result->getAfter();
        }
        if ($result->getBefore() !== null) {
            $data['cursors']['before'] = $result->getBefore();
        }

        if ($filter === null) {
            $data['offset'] = 0;
        } elseif ($filter->getOffset() !== null) {
            $data['offset'] = $filter->getOffset();
        }

        if ($result->hasNext() !== null) {
            $data['has_next'] = $result->hasNext();
        }

        if ($result->hasPrevious() !== null) {
            $data['has_previous'] = $result->hasPrevious();
        }

        return $data;
    }
}
