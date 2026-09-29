<?php

namespace Paysera\Component\Serializer\Tests\Entity;

use Paysera\Component\Serializer\Entity\FollowUpFilter;
use PHPUnit\Framework\TestCase;

class FollowUpFilterTest extends TestCase
{
    /**
     * @dataProvider filterProvider
     */
    public function testState(FollowUpFilter $filter, array $expected)
    {
        $this->assertSame(
            $expected,
            [
                'remainingCount' => $filter->getRemainingCount(),
                'offset' => $filter->getOffset(),
                'shouldFollowUp' => $filter->shouldFollowUp(),
            ]
        );
    }

    public static function filterProvider()
    {
        return [
            'items remain' => [
                new FollowUpFilter(3, 0),
                ['remainingCount' => 3, 'offset' => 0, 'shouldFollowUp' => true],
            ],
            'nothing remains' => [
                new FollowUpFilter(0, 0),
                ['remainingCount' => 0, 'offset' => 0, 'shouldFollowUp' => false],
            ],
            'offset kept when a cursor is set' => [
                (new FollowUpFilter(1, 10))->setAfter('cursor'),
                ['remainingCount' => 1, 'offset' => 10, 'shouldFollowUp' => true],
            ],
            'remaining count decreased' => [
                (new FollowUpFilter(5, 10))->decreaseRemainingCount(2),
                ['remainingCount' => 3, 'offset' => 10, 'shouldFollowUp' => true],
            ],
            'remaining count decreased past zero' => [
                (new FollowUpFilter(5, 10))->decreaseRemainingCount(10),
                ['remainingCount' => 0, 'offset' => 10, 'shouldFollowUp' => false],
            ],
            'offset decreased' => [
                (new FollowUpFilter(5, 10))->decreaseOffset(4),
                ['remainingCount' => 5, 'offset' => 6, 'shouldFollowUp' => true],
            ],
            'offset decreased past zero' => [
                (new FollowUpFilter(5, 10))->decreaseOffset(100),
                ['remainingCount' => 5, 'offset' => 0, 'shouldFollowUp' => true],
            ],
        ];
    }

    public function testDecreaseMethodsAreFluent()
    {
        $filter = new FollowUpFilter(5, 10);

        $this->assertSame($filter, $filter->decreaseRemainingCount(1));
        $this->assertSame($filter, $filter->decreaseOffset(1));
    }
}
