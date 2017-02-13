<?php

namespace Paysera\Component\Serializer\Normalizer;

use Paysera\Component\Serializer\Entity\Filter;
use Paysera\Component\Serializer\Entity\NormalizationContextInterface;
use Paysera\Component\Serializer\Entity\Result;

class ResultNormalizer implements ContextAwareNormalizerInterface, DenormalizerInterface
{
    /**
     * @var NormalizerInterface
     */
    protected $metadataNormalizer;

    /**
     * @var NormalizerInterface|DenormalizerInterface
     */
    protected $itemsNormalizer;

    /**
     * @var string
     */
    protected $itemsKey;


    /**
     * @param string                                    $itemsKey
     * @param NormalizerInterface|DenormalizerInterface $itemNormalizer
     */
    public function __construct($itemsKey, $itemNormalizer)
    {
        $this->itemsNormalizer = new ArrayNormalizer($itemNormalizer);
        $this->itemsKey = $itemsKey;
    }

    /**
     * Sets metadataNormalizer
     *
     * @param NormalizerInterface $metadataNormalizer
     *
     * @return $this
     */
    public function setMetadataNormalizer($metadataNormalizer)
    {
        $this->metadataNormalizer = $metadataNormalizer;

        return $this;
    }

    /**
     * Maps some structure to raw data. Usually entity object to array
     *
     * @param mixed $entity
     * @param \Paysera\Component\Serializer\Entity\NormalizationContextInterface $context
     *
     * @return mixed
     */
    public function mapFromEntity($entity, NormalizationContextInterface $context = null)
    {
        return array(
            $this->itemsKey => $this->mapItemsFromEntity(
                $entity->getItems(),
                $context !== null ? $context->createScopedContext($this->itemsKey) : null
            ),
            '_metadata' => $this->mapMetadataFromEntity($entity),
        );
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
        return $this->mapBaseKeys($data, new Result());
    }

    protected function mapBaseKeys($data, Result $result)
    {
        $filter = new Filter();
        $metadata = isset($data['_metadata']) ? $data['_metadata'] : null;
        if ($metadata !== null) {
            if (isset($metadata['total'])) {
                $result->setTotalCount($metadata['total']);
            }

            if (isset($metadata['limit'])) {
                $filter->setLimit($metadata['limit']);
            }

            if (isset($metadata['offset'])) {
                $filter->setOffset($metadata['offset']);
            }

            if (isset($metadata['cursors'])) {
                if (isset($metadata['cursors']['before'])) {
                    $result->setBefore($metadata['cursors']['before']);
                }

                if (isset($metadata['cursors']['after'])) {
                    $result->setAfter($metadata['cursors']['after']);
                }
            }

            if (isset($metadata['has_next'])) {
                $result->setHasNext($metadata['has_next']);
            }

            if (isset($metadata['has_previous'])) {
                $result->setHasPrevious($metadata['has_previous']);
            }
        }

        $result->setFilter($filter);
        $result->setItems($this->itemsNormalizer->mapToEntity($data[$this->itemsKey]));

        return $result;
    }

    protected function mapMetadataFromEntity(Result $result)
    {
        return $this->metadataNormalizer->mapFromEntity($result);
    }

    protected function mapItemsFromEntity($items, NormalizationContextInterface $context = null)
    {
        return $this->itemsNormalizer->mapFromEntity($items, $context);
    }
}
