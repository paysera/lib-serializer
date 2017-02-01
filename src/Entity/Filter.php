<?php

namespace Paysera\Component\Serializer\Entity;

class Filter
{
    /**
     * Should be one of ORDER_BY_* constants in descendant classes
     * null means skip ordering
     * @var string
     */
    protected $orderBy;

    /**
     * true - ascending, false - descending
     * @var boolean
     */
    protected $orderAsc;

    /**
     * @var int
     */
    protected $offset = 0;

    /**
     * null means no limit
     * @var int
     */
    protected $limit;

    /**
     * @var string|null
     */
    protected $after;

    /**
     * @var string
     */
    protected $before;

    /**
     * Sets orderBy
     *
     * @param string $orderBy
     *
     * @return self
     */
    public function setOrderBy($orderBy)
    {
        $this->orderBy = $orderBy;
        return $this;
    }

    /**
     * Gets orderBy
     *
     * @return string
     */
    public function getOrderBy()
    {
        return $this->orderBy;
    }

    /**
     * Sets orderAsc
     *
     * @param boolean $orderAsc
     *
     * @return self
     */
    public function setOrderAsc($orderAsc)
    {
        $this->orderAsc = $orderAsc;
        return $this;
    }

    /**
     * Gets orderAsc
     *
     * @return boolean
     */
    public function isOrderAsc()
    {
        return $this->orderAsc;
    }

    /**
     * Gets ordering direction
     *
     * @return string    ASC | DESC
     */
    public function getOrderDirection()
    {
        return $this->orderAsc ? 'ASC' : 'DESC';
    }

    /**
     * Sets offset
     *
     * @param int $offset
     *
     * @return self
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Gets offset
     *
     * @return int|null
     */
    public function getOffset()
    {
        return ($this->getAfter() !== null || $this->getBefore() !== null) ? null : $this->offset;
    }

    /**
     * Sets limit
     *
     * @param int $limit
     *
     * @return self
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Gets limit
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @return string
     */
    public function getAfter()
    {
        return $this->after;
    }

    /**
     * @param string $after
     * @return $this
     */
    public function setAfter($after)
    {
        $this->after = $after;
        return $this;
    }

    /**
     * @return string
     */
    public function getBefore()
    {
        return $this->before;
    }

    /**
     * @param string $before
     * @return $this
     */
    public function setBefore($before)
    {
        $this->before = $before;
        return $this;
    }

    /**
     * Creates new instance of this filter
     *
     * @return static
     */
    public static function create()
    {
        return new static();
    }
}
