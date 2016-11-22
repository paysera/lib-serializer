<?php

namespace Paysera\Component\Serializer\Entity;

interface ResultInterface extends \Traversable
{
    /**
     * Gets totalCount
     *
     * @return int
     */
    public function getTotalCount();


    /**
     * Gets items loaded by the provided filter
     *
     * @return array
     */
    public function getItems();

    /**
     * Gets filter
     *
     * @return Filter
     */
    public function getFilter();
}
