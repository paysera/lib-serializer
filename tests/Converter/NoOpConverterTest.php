<?php

namespace Paysera\Component\Serializer\Tests\Converter;

use Paysera\Component\Serializer\Converter\NoOpConverter;
use PHPUnit\Framework\TestCase;

class NoOpConverterTest extends TestCase
{
    /**
     * @param mixed $path
     *
     * @dataProvider pathProvider
     */
    public function testConvertReturnsPathUnchanged($path)
    {
        $this->assertSame($path, (new NoOpConverter())->convert($path));
    }

    public static function pathProvider()
    {
        return [
            'camel case' => ['firstName'],
            'snake case' => ['first_name'],
            'nested path' => ['address.streetName'],
            'null' => [null],
        ];
    }
}
