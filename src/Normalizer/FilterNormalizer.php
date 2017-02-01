<?php

namespace Paysera\Component\Serializer\Normalizer;

use Paysera\Component\Serializer\Entity\Filter;
use Paysera\Component\Serializer\Exception\InvalidDataException;

class FilterNormalizer extends BaseDenormalizer implements NormalizerInterface
{
    protected $defaultLimit;
    protected $maxLimit;
    protected $orderByFields;

    public function __construct($orderByFields = array(), $defaultLimit = 20, $maxLimit = 200)
    {
        $this->defaultLimit = $defaultLimit;
        $this->maxLimit = $maxLimit;
        $this->orderByFields = $orderByFields;
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
        return $this->mapBaseKeys($data, new Filter());
    }

    /**
     * Maps some structure to raw data. Usually entity object to array
     *
     * @param Filter $entity
     *
     * @return mixed
     */
    public function mapFromEntity($entity)
    {
        $data = array();
        if ($entity->getLimit() !== null) {
            $data['limit'] = $entity->getLimit();
        }
        if ($entity->getOffset() !== null) {
            $data['offset'] = $entity->getOffset();
        }
        if ($entity->getOrderBy() !== null) {
            $data['order_by'] = $entity->getOrderBy();
        }
        if ($entity->isOrderAsc() !== null) {
            $data['order_direction'] = $entity->isOrderAsc() ? 'asc' : 'desc';
        }
        if ($entity->getAfter() !== null) {
            $data['after'] = $entity->getAfter();
        }
        if ($entity->getBefore() !== null) {
            $data['before'] = $entity->getBefore();
        }
        return $data;
    }


    protected function mapBaseKeys($data, Filter $filter)
    {
        $limit = isset($data['limit']) ? $data['limit'] : null;
        $offset = isset($data['offset']) ? $data['offset'] : null;
        $after = isset($data['after']) ? $data['after'] : null;
        $before = isset($data['before']) ? $data['before'] : null;
        $orderBy = isset($data['order_by']) ? $data['order_by'] : null;
        $orderDirection = isset($data['order_direction']) ? $data['order_direction'] : null;


        if (
            ($before !== null && $after !== null)
            || ($before !== null && $offset !== null)
            || ($after !== null && $offset !== null)
        ) {
            throw new InvalidDataException('Only one cursor is supported');
        }

        if ($limit !== null) {
            if ((string)$limit !== (string)(int)$limit) {
                throw new InvalidDataException('Invalid parameter: limit');
            }
            $limit = (int)$limit;
            if ($limit > $this->maxLimit) {
                throw new InvalidDataException('limit cannot exceed ' . $this->maxLimit);
            }
            if ($limit < 0) {
                throw new InvalidDataException('limit cannot be negative');
            }
        } else {
            $limit = $this->defaultLimit;
        }
        $filter->setLimit($limit);

        if ($offset !== null) {
            if ((string)$offset !== (string)(int)$offset) {
                throw new InvalidDataException('Invalid parameter: offset');
            }
            $offset = (int)$offset;
            if ($offset < 0) {
                throw new InvalidDataException('offset cannot be negative');
            }

            $filter->setOffset($offset);
        }

        $filter->setBefore($before);
        $filter->setAfter($after);

        if ($orderBy) {
            if (!in_array($orderBy, $this->orderByFields, true)) {
                throw new InvalidDataException('Unsupported order_by value');
            }
            $filter->setOrderBy($orderBy);
        }
        if ($orderDirection) {
            if (count($this->orderByFields) === 0) {
                throw new InvalidDataException('order_direction is unsupported for this method');
            }
            $orderDirection = strtoupper($orderDirection);
            if (!in_array($orderDirection, array('ASC', 'DESC'))) {
                throw new InvalidDataException('Invalid order_direction value');
            }
            $filter->setOrderAsc($orderDirection === 'ASC');
        }

        return $filter;
    }
}
