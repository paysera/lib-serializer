<?php

namespace Paysera\Component\Serializer\Tests\Normalizer;

use Paysera\Component\Serializer\Entity\Violation;
use Paysera\Component\Serializer\Normalizer\ViolationNormalizer;
use PHPUnit\Framework\TestCase;

class ViolationNormalizerTest extends TestCase
{
    public function testMapToEntity()
    {
        $violation = (new ViolationNormalizer())->mapToEntity([
            'code' => 'not_blank',
            'message' => 'This value should not be blank.',
            'field' => 'email',
        ]);

        $this->assertSame('not_blank', $violation->getCode());
        $this->assertSame('This value should not be blank.', $violation->getMessage());
        $this->assertSame('email', $violation->getField());
    }

    public function testMapToEntityIgnoresMissingNullAndUnknownKeys()
    {
        $violation = (new ViolationNormalizer())->mapToEntity(['code' => null, 'unknown' => 'value']);

        $this->assertEquals(new Violation(), $violation);
    }

    public function testMapFromEntity()
    {
        $violation = (new Violation())
            ->setField('email')
            ->setMessage('This value should not be blank.')
            ->setCode('not_blank')
        ;

        $this->assertSame(
            [
                'code' => 'not_blank',
                'message' => 'This value should not be blank.',
                'field' => 'email',
            ],
            (new ViolationNormalizer())->mapFromEntity($violation)
        );
    }

    public function testMapFromEntityOmitsNullFields()
    {
        $this->assertSame([], (new ViolationNormalizer())->mapFromEntity(new Violation()));
        $this->assertSame(
            ['message' => 'Invalid'],
            (new ViolationNormalizer())->mapFromEntity((new Violation())->setMessage('Invalid'))
        );
    }
}
