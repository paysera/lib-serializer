<?php

namespace Paysera\Component\Serializer\Tests\Converter;

use Paysera\Component\Serializer\Converter\CamelCaseToSnakeCaseConverter;
use PHPUnit\Framework\TestCase;

class CamelCaseToSnakeCaseConverterTest extends TestCase
{
    /**
     * @var CamelCaseToSnakeCaseConverter
     */
    protected $camelCaseToSnakeCaseConverter;

    protected function setUp(): void
    {
        $this->camelCaseToSnakeCaseConverter = new CamelCaseToSnakeCaseConverter();
    }

    /**
     * @param string $data
     * @param string $result
     *
     * @dataProvider converterDataProvider
     */
    public function testConverter($data, $result)
    {
        $this->assertEquals($result, $this->camelCaseToSnakeCaseConverter->convert($data));
    }

    public function converterDataProvider()
    {
        return [
            [
                'data' => 'firstName',
                'result' => 'first_name',
            ],
            [
                'data' => 'FirstName',
                'result' => 'first_name',
            ],
            [
                'data' => 'first_name',
                'result' => 'first_name',
            ],
            [
                'data' => 'Age',
                'result' => 'age',
            ],
            [
                'data' => null,
                'result' => '',
            ]
        ];
    }
}
