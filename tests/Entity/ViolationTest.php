<?php

namespace Paysera\Component\Serializer\Tests\Entity;

use Paysera\Component\Serializer\Entity\Violation;
use PHPUnit\Framework\TestCase;

class ViolationTest extends TestCase
{
    /**
     * @dataProvider violationProvider
     */
    public function testGetters(Violation $violation, array $expected)
    {
        $this->assertSame(
            $expected,
            [
                'field' => $violation->getField(),
                'code' => $violation->getCode(),
                'message' => $violation->getMessage(),
            ]
        );
    }

    public static function violationProvider()
    {
        return [
            'new' => [new Violation(), ['field' => null, 'code' => null, 'message' => null]],
            'all set' => [
                (new Violation())
                    ->setField('email')
                    ->setCode('not_blank')
                    ->setMessage('This value should not be blank.'),
                ['field' => 'email', 'code' => 'not_blank', 'message' => 'This value should not be blank.'],
            ],
        ];
    }

    public function testSettersAreFluent()
    {
        $violation = new Violation();

        $this->assertSame($violation, $violation->setField('email'));
        $this->assertSame($violation, $violation->setCode('not_blank'));
        $this->assertSame($violation, $violation->setMessage('This value should not be blank.'));
    }
}
