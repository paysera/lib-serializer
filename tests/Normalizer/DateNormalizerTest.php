<?php

namespace Paysera\Component\Serializer\Tests\Normalizer;

use Paysera\Component\Serializer\Exception\InvalidDataException;
use Paysera\Component\Serializer\Normalizer\DateNormalizer;
use PHPUnit\Framework\TestCase;

class DateNormalizerTest extends TestCase
{
    public function setUp(): void
    {
        date_default_timezone_set('Etc/GMT-2'); //same as UTC+2
    }


    public function testMapToEntity_when_no_correction_by_timezone_needed_then_date_not_modified()
    {
        $service = new DateNormalizer('Y-m-d H:i:s');

        $result = $service->mapToEntity('2013-02-01 12:00:00');
        $this->assertEquals(new \DateTime('2013-02-01 12:00:00', new \DateTimeZone('Etc/GMT-2')),  $result);

        $result = $service->mapFromEntity(new \DateTime('2013-02-01 12:00:00'));
        $this->assertEquals('2013-02-01 12:00:00',  $result);
    }

    public function testMapToEntity_when_correction_by_timezone_needed_then_date_modified()
    {
        $service = new DateNormalizer('Y-m-d H:i:s', new \DateTimeZone('Etc/GMT+0'));

        $result = $service->mapToEntity('2013-02-01 12:00:00');
        $this->assertEquals(new \DateTime('2013-02-01 14:00:00'),  $result);

        $result = $service->mapFromEntity(new \DateTime('2013-02-01 14:00:00'));
        $this->assertEquals('2013-02-01 12:00:00',  $result);
    }

    public function testMapToEntity_original_entity_not_modified_when_mapping_from_entity()
    {
        $service = new DateNormalizer('Y-m-d H:i:s', new \DateTimeZone('Etc/GMT+0'));

        $datetimeOriginal = new \DateTime('2013-02-01 14:00:00');
        $datetime = clone $datetimeOriginal;
        $service->mapFromEntity($datetime);

        $this->assertEquals($datetimeOriginal, $datetime);
    }

    public function testMapToEntity_mapping_from_null_entity_returns_null()
    {
        $service = new DateNormalizer('Y-m-d H:i:s', new \DateTimeZone('Etc/GMT+0'));

        $datetime = null;
        $result = $service->mapFromEntity($datetime);

        $this->assertNull($result);
    }

    public function testMapToEntity_invalid_date_throws_exception()
    {
        $service = new DateNormalizer('Y-m-d H:i:s', new \DateTimeZone('Etc/GMT+0'));

        $datetime = null;
        $this->expectException(InvalidDataException::class);
        $service->mapToEntity('2013-02-31 12:00:00');
    }
}
