<?php

namespace Paysera\Component\Serializer\Normalizer;

class CollectionNormalizer
{
    private $normalizer;

    /**
     * CollectionNormalizer constructor.
     *
     * @param NormalizerInterface $normalizer
     */
    public function __construct(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /**
     * Normalize all items in collection, render metadata and return associative array with:
     *      'items'     => array of normalized items,
     *      '_metadata' => collection metadata
     *
     * @param mixed $collection
     *
     * @return array
     */
    public function mapFromEntity($collection)
    {
        $data = [];
        foreach ($collection as $item) {
            $data[] = $this->normalizer->mapFromEntity($item);
        }

        return $this->renderResult($data);
    }

    /**
     * Render associative array from given data.
     *
     * @param array $data
     *
     * @return array
     */
    private function renderResult(array $data)
    {
        return [
            'items'     => $data,
            '_metadata' => $this->renderMetaData($data),
        ];
    }

    /**
     * Render metadata array from given data
     *
     * @param array $data
     *
     * @return array
     */
    private function renderMetaData(array $data)
    {
        return [
            'total'  => count($data),
            'offset' => 0,
            'limit'  => null,
        ];
    }
}
