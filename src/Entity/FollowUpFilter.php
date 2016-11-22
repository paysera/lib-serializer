<?php

namespace Paysera\Component\Serializer\Entity;

class FollowUpFilter extends Filter
{
    /**
     * @var int
     */
    protected $offset;

    /**
     * @var int
     */
    protected $remainingCount;

    public function __construct($remainingCount, $offset)
    {
        $this->remainingCount = $remainingCount;
        $this->offset = $offset;
    }

    public function getOffset()
    {
        return $this->offset;
    }

    public function shouldFollowUp()
    {
        return $this->remainingCount > 0;
    }

    public function getRemainingCount()
    {
        return $this->remainingCount;
    }

    public function decreaseRemainingCount($count)
    {
        $this->remainingCount = max(0, $this->remainingCount - $count);

        return $this;
    }

    public function decreaseOffset($count)
    {
        $this->offset = max(0, $this->offset - $count);

        return $this;
    }
}
