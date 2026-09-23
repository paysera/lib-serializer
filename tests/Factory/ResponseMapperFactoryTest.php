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

    public function testDefaultMapperIsReturnedWithoutOptions()
    {
        $this->assertSame($this->defaultMapper, $this->factory->createResponseMapper([]));
    }

    public function testMapperOptionSelectsMapper()
    {
        $this->assertSame($this->fullMapper, $this->factory->createResponseMapper(['mapper' => 'full']));
    }

    public function testMapperOptionWinsOverFlags()
    {
        $this->assertSame(
            $this->shortMapper,
            $this->factory->createResponseMapper(['mapper' => 'short', 'full' => true])
        );
    }

    public function testUnknownMapperOptionThrows()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Wrong mapper key specified: missing');

        $this->factory->createResponseMapper(['mapper' => 'missing']);
    }

    public function testNullMapperOptionFallsBackToDefault()
    {
        $this->assertSame($this->defaultMapper, $this->factory->createResponseMapper(['mapper' => null]));
    }

    public function testFlagSetToTrueSelectsMapper()
    {
        $this->assertSame($this->shortMapper, $this->factory->createResponseMapper(['short' => true]));
    }

    /**
     * @dataProvider ignoredFlagProvider
     */
    public function testFlagsThatAreNotStrictlyTrueOrUnknownAreIgnored(array $options)
    {
        $this->assertSame($this->defaultMapper, $this->factory->createResponseMapper($options));
    }

    public static function ignoredFlagProvider()
    {
        return [
            'string one' => [['short' => '1']],
            'integer one' => [['short' => 1]],
            'false' => [['short' => false]],
            'unknown mapper' => [['unknown' => true]],
        ];
    }
}
