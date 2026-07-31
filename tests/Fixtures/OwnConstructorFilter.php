<?php

namespace Paysera\Component\Serializer\Tests\Fixtures;

use Paysera\Component\Serializer\Entity\Filter;

/**
 * Filter has no constructor and is designed for subclassing, so descendants are
 * not obliged to call parent::__construct(). Pins that contract down: the offset
 * default has to live on the property declaration for this to keep working.
 */
class OwnConstructorFilter extends Filter
{
    /**
     * @var string|null
     */
    protected $status;

    public function __construct($status = null)
    {
        $this->status = $status;
    }
}
