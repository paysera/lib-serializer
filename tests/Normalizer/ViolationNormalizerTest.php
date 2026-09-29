<?php

namespace Paysera\Component\Serializer\Tests\Normalizer;

use Paysera\Component\Serializer\Entity\Violation;
use Paysera\Component\Serializer\Normalizer\ViolationNormalizer;
use PHPUnit\Framework\TestCase;

class ViolationNormalizerTest extends TestCase
{
    /**
     * @dataProvider mapToEntityProvider
     */
    public function testMapToEntity(array $data, Violation $expected)
    {
        $this->assertEquals($expected, (new ViolationNormalizer())->mapToEntity($data));
    }

    public static function mapToEntityProvider()
    {
        return [
            'all keys' => [
                ['code' => 'not_blank', 'message' => 'This value should not be blank.', 'field' => 'email'],
                (new Violation())
                    ->setCode('not_blank')
                    ->setMessage('This value should not be blank.')
                    ->setField('email'),
            ],
            'missing, null and unknown keys' => [['code' => null, 'unknown' => 'value'], new Violation()],
        ];
    }

    /**
     * @dataProvider mapFromEntityProvider
     */
    public function testMapFromEntity(Violation $violation, array $expected)
    {
        $this->assertSame($expected, (new ViolationNormalizer())->mapFromEntity($violation));
    }

    public static function mapFromEntityProvider()
    {
        return [
            'all fields' => [
                (new Violation())
                    ->setField('email')
                    ->setMessage('This value should not be blank.')
                    ->setCode('not_blank'),
                ['code' => 'not_blank', 'message' => 'This value should not be blank.', 'field' => 'email'],
            ],
            'no fields' => [new Violation(), []],
            'message only' => [(new Violation())->setMessage('Invalid'), ['message' => 'Invalid']],
        ];
    }
}
