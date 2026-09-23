<?php

namespace Paysera\Component\Serializer\Tests\Normalizer;

use Paysera\Component\Serializer\Exception\InvalidDataException;
use Paysera\Component\Serializer\Tests\Fixtures\KeyCheckingDenormalizer;
use PHPUnit\Framework\TestCase;

class BaseDenormalizerTest extends TestCase
{
    /**
     * @var KeyCheckingDenormalizer
     */
    private $denormalizer;

    public function setUp(): void
    {
        $this->denormalizer = new KeyCheckingDenormalizer();
    }

    /**
     * @dataProvider checkProvider
     */
    public function testChecksRejectContentThatIsNotArray($check)
    {
        $this->expectException(InvalidDataException::class);
        $this->expectExceptionMessage('Content is expected to be array');

        $this->denormalizer->$check('text', ['a']);
    }

    public static function checkProvider()
    {
        return [
            'available keys' => ['checkAvailableKeys'],
            'required keys' => ['checkRequiredKeys'],
            'only one key' => ['checkOnlyOneKeyExists'],
        ];
    }

    public function testCheckAvailableKeysAcceptsKnownKeys()
    {
        $this->expectNotToPerformAssertions();

        $this->denormalizer->checkAvailableKeys(['a' => 1], ['a', 'b']);
    }

    public function testCheckAvailableKeysListsUnknownKeys()
    {
        $this->expectException(InvalidDataException::class);
        $this->expectExceptionMessage('Some keys in item are not available: c, d');

        $this->denormalizer->checkAvailableKeys(['a' => 1, 'c' => 2, 'd' => 3], ['a', 'b']);
    }

    public function testIgnoredAvailableKeysCheckAcceptsUnknownKeys()
    {
        $this->denormalizer->ignoreAvailableKeysCheck();

        $this->expectNotToPerformAssertions();

        $this->denormalizer->checkAvailableKeys(['c' => 2], ['a']);
    }

    public function testIgnoredAvailableKeysCheckStillRejectsContentThatIsNotArray()
    {
        $this->denormalizer->ignoreAvailableKeysCheck();

        $this->expectException(InvalidDataException::class);
        $this->expectExceptionMessage('Content is expected to be array');

        $this->denormalizer->checkAvailableKeys(null, ['a']);
    }

    public function testCheckRequiredKeysAcceptsPresentKeys()
    {
        $this->expectNotToPerformAssertions();

        $this->denormalizer->checkRequiredKeys(['a' => 1, 'b' => 0, 'c' => 3], ['a', 'b']);
    }

    public function testCheckRequiredKeysTreatsNullAsMissing()
    {
        $this->expectException(InvalidDataException::class);
        $this->expectExceptionMessage('Key b is required');

        $this->denormalizer->checkRequiredKeys(['a' => 1, 'b' => null], ['a', 'b']);
    }

    public function testCheckOnlyOneKeyExistsAcceptsOneKeyAndIgnoresNullValues()
    {
        $this->expectNotToPerformAssertions();

        $this->denormalizer->checkOnlyOneKeyExists(['a' => 1, 'b' => null, 'x' => 2], ['a', 'b', 'c']);
    }

    public function testCheckOnlyOneKeyExistsRejectsSecondKey()
    {
        $this->expectException(InvalidDataException::class);
        $this->expectExceptionMessage('Only one of a, b, c can be provided');

        $this->denormalizer->checkOnlyOneKeyExists(['a' => 1, 'c' => 2], ['a', 'b', 'c']);
    }
}
