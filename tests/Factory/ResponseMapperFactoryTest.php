<?php

namespace Paysera\Component\Serializer\Tests\Factory;

use Paysera\Component\Serializer\Factory\ResponseMapperFactory;
use Paysera\Component\Serializer\Normalizer\NormalizerInterface;
use Paysera\Component\Serializer\Normalizer\PlainNormalizer;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ResponseMapperFactoryTest extends TestCase
{
    /**
     * @var NormalizerInterface
     */
    private $defaultMapper;

    /**
     * @var NormalizerInterface
     */
    private $shortMapper;

    /**
     * @var NormalizerInterface
     */
    private $fullMapper;

    /**
     * @var ResponseMapperFactory
     */
    private $factory;

    public function setUp(): void
    {
        $this->defaultMapper = new PlainNormalizer();
        $this->shortMapper = new PlainNormalizer();
        $this->fullMapper = new PlainNormalizer();

        $this->factory = new ResponseMapperFactory($this->defaultMapper);
        $this->factory->addMapper('short', $this->shortMapper);
        $this->factory->addMapper('full', $this->fullMapper);
    }

    public function testAddMapperIsFluent()
    {
        $this->assertSame($this->factory, $this->factory->addMapper('other', new PlainNormalizer()));
    }

    /**
     * @dataProvider optionsProvider
     */
    public function testCreateResponseMapper(array $options, $expectedMapper)
    {
        $mappers = ['default' => $this->defaultMapper, 'short' => $this->shortMapper, 'full' => $this->fullMapper];

        $this->assertSame($mappers[$expectedMapper], $this->factory->createResponseMapper($options));
    }

    public static function optionsProvider()
    {
        return [
            'no options' => [[], 'default'],
            'mapper option' => [['mapper' => 'full'], 'full'],
            'mapper option wins over flags' => [['mapper' => 'short', 'full' => true], 'short'],
            'null mapper option' => [['mapper' => null], 'default'],
            'flag set to true' => [['short' => true], 'short'],
            'two true flags, full listed last' => [['short' => true, 'full' => true], 'full'],
            'two true flags, short listed last' => [['full' => true, 'short' => true], 'short'],
            'later true flag naming no mapper' => [['short' => true, 'unknown' => true], 'short'],
            'later flag that is false' => [['short' => true, 'full' => false], 'short'],
            'flag set to string one' => [['short' => '1'], 'default'],
            'flag set to integer one' => [['short' => 1], 'default'],
            'flag set to false' => [['short' => false], 'default'],
            'true flag naming no mapper' => [['unknown' => true], 'default'],
        ];
    }

    public function testUnknownMapperOptionThrows()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Wrong mapper key specified: missing');

        $this->factory->createResponseMapper(['mapper' => 'missing']);
    }
}
