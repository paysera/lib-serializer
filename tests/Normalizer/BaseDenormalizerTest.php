<?php

namespace Paysera\Component\Serializer\Tests\Normalizer;

use Paysera\Component\Serializer\Exception\InvalidDataException;
use Paysera\Component\Serializer\Tests\Fixtures\KeyCheckingDenormalizer;
use PHPUnit\Framework\TestCase;

class BaseDenormalizerTest extends TestCase
{
    /**
     * @dataProvider acceptedProvider
     */
    public function testCheckAccepts($check, array $data, array $keys)
    {
        $this->expectNotToPerformAssertions();

        (new KeyCheckingDenormalizer())->$check($data, $keys);
    }

    public static function acceptedProvider()
    {
        return [
            'known keys' => ['checkAvailableKeys', ['a' => 1], ['a', 'b']],
            'required keys present' => ['checkRequiredKeys', ['a' => 1, 'b' => 0, 'c' => 3], ['a', 'b']],
            'one of the keys, null values ignored' => [
                'checkOnlyOneKeyExists',
                ['a' => 1, 'b' => null, 'x' => 2],
                ['a', 'b', 'c'],
            ],
        ];
    }

    /**
     * @dataProvider rejectedProvider
     */
    public function testCheckRejects($check, $data, array $keys, $message)
    {
        $this->expectException(InvalidDataException::class);
        $this->expectExceptionMessage($message);

        (new KeyCheckingDenormalizer())->$check($data, $keys);
    }

    public static function rejectedProvider()
    {
        return [
            'available keys of content that is not an array' => [
                'checkAvailableKeys',
                'text',
                ['a'],
                'Content is expected to be array',
            ],
            'required keys of content that is not an array' => [
                'checkRequiredKeys',
                'text',
                ['a'],
                'Content is expected to be array',
            ],
            'only one key of content that is not an array' => [
                'checkOnlyOneKeyExists',
                'text',
                ['a'],
                'Content is expected to be array',
            ],
            'unknown keys' => [
                'checkAvailableKeys',
                ['a' => 1, 'c' => 2, 'd' => 3],
                ['a', 'b'],
                'Some keys in item are not available: c, d',
            ],
            'required key set to null' => [
                'checkRequiredKeys',
                ['a' => 1, 'b' => null],
                ['a', 'b'],
                'Key b is required',
            ],
            'second of the keys' => [
                'checkOnlyOneKeyExists',
                ['a' => 1, 'c' => 2],
                ['a', 'b', 'c'],
                'Only one of a, b, c can be provided',
            ],
        ];
    }

    public function testIgnoredAvailableKeysCheckAcceptsUnknownKeys()
    {
        $denormalizer = new KeyCheckingDenormalizer();
        $denormalizer->ignoreAvailableKeysCheck();

        $this->expectNotToPerformAssertions();

        $denormalizer->checkAvailableKeys(['c' => 2], ['a']);
    }

    public function testIgnoredAvailableKeysCheckStillRejectsContentThatIsNotArray()
    {
        $denormalizer = new KeyCheckingDenormalizer();
        $denormalizer->ignoreAvailableKeysCheck();

        $this->expectException(InvalidDataException::class);
        $this->expectExceptionMessage('Content is expected to be array');

        $denormalizer->checkAvailableKeys(null, ['a']);
    }
}
