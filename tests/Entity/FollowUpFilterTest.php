<?php

namespace Paysera\Component\Serializer\Tests\Entity;

use Paysera\Component\Serializer\Entity\FollowUpFilter;
use PHPUnit\Framework\TestCase;

class FollowUpFilterTest extends TestCase
{
    public function testShouldFollowUpWhileItemsRemain()
    {
        $filter = new FollowUpFilter(3, 0);

        $this->assertSame(3, $filter->getRemainingCount());
        $this->assertTrue($filter->shouldFollowUp());
    }

    public function testShouldNotFollowUpWhenNothingRemains()
    {
        $this->assertFalse((new FollowUpFilter(0, 0))->shouldFollowUp());
    }

    public function testDecreaseRemainingCountStopsAtZero()
    {
        $filter = new FollowUpFilter(5, 0);

        $this->assertSame($filter, $filter->decreaseRemainingCount(2));
        $this->assertSame(3, $filter->getRemainingCount());

        $filter->decreaseRemainingCount(10);
        $this->assertSame(0, $filter->getRemainingCount());
        $this->assertFalse($filter->shouldFollowUp());
    }

    public function testDecreaseOffsetStopsAtZero()
    {
        $filter = new FollowUpFilter(1, 10);

        $this->assertSame($filter, $filter->decreaseOffset(4));
        $this->assertSame(6, $filter->getOffset());

        $filter->decreaseOffset(100);
        $this->assertSame(0, $filter->getOffset());
    }

    /**
     * Unlike Filter, the follow-up offset is not hidden by a cursor.
     */
    public function testOffsetIsReturnedEvenWhenCursorIsSet()
    {
        $filter = (new FollowUpFilter(1, 10))->setAfter('cursor');

        $this->assertSame(10, $filter->getOffset());
    }
}
