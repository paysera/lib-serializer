<?php

namespace Paysera\Component\Serializer\Entity;

class Result implements \IteratorAggregate, ResultInterface
{
    /**
     * @var int
     */
    protected $totalCount = 0;

    /**
     * @var boolean|null
     */
    protected $hasNext;

    /**
     * @var boolean|null
     */
    protected $hasPrevious;

    /**
     * @var string|null
     */
    protected $after;

    /**
     * @var string|null
     */
    protected $before;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var mixed[]
     */
    protected $items;


    public function __construct(?Filter $filter = null)
    {
        $this->filter = $filter;
    }

    /**
     * Sets totalCount
     *
     * @param int $totalCount
     *
     * @return self
     */
    public function setTotalCount($totalCount)
    {
        $this->totalCount = (int)$totalCount;
        return $this;
    }

    /**
     * Gets totalCount
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     * @return bool
     */
    public function hasNext()
    {
        return $this->hasNext;
    }

    /**
     * @param bool $hasNext
     * @return $this
     */
    public function setHasNext($hasNext)
    {
        $this->hasNext = $hasNext;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasPrevious()
    {
        return $this->hasPrevious;
    }

    /**
     * @param bool $hasPrevious
     * @return $this
     */
    public function setHasPrevious($hasPrevious)
    {
        $this->hasPrevious = $hasPrevious;
        return $this;
    }

    /**
     * Sets filter
     *
     * @param Filter $filter
     *
     * @return self
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;
        return $this;
    }

    /**
     * Gets filter
     *
     * @return Filter
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @return null|string
     */
    public function getAfter()
    {
        return $this->after;
    }

    /**
     * @param null|string $after
     * @return $this
     */
    public function setAfter($after)
    {
        $this->after = $after;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getBefore()
    {
        return $this->before;
    }

    /**
     * @param null|string $before
     * @return $this
     */
    public function setBefore($before)
    {
        $this->before = $before;
        return $this;
    }

    /**
     * Sets items
     *
     * @param array $items
     *
     * @return $this
     */
    public function setItems($items)
    {
        $this->items = $items;

        return $this;
    }

    /**
     * Gets items
     *
     * @return mixed[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param mixed $item
     *
     * @return $this
     */
    public function addItem($item)
    {
        $this->items[] = $item;
        return $this;
    }

    /**
     * Try to calculate total result count, in case all results are fetched.
     *
     * @param $resultCount
     * @return null
     * @throws \BadMethodCallException
     */
    public function calculateTotalCount($resultCount)
    {
        if (!$this->getFilter()) {
            throw new \BadMethodCallException('filter must be set before calling this method');
        }

        if (
            $this->getFilter()->getOffset() !== null
            && ($this->getFilter()->getLimit() === null || $resultCount < (int)$this->getFilter()->getLimit())
            && ($resultCount !== 0 || $this->getFilter()->getOffset() === 0)
        ) {
            $this->totalCount = $resultCount + $this->getFilter()->getOffset();
            return $this->totalCount;
        }

        return null;
    }

    /**
     * Retrieve an external iterator
     *
     * @return \Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }
}
