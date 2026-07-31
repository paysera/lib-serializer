# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## 3.5.0
### Changed
- `DateNormalizer::mapToEntity()` rejects `null` up front instead of passing it to
  `DateTime::createFromFormat()`, resolving a PHP 8.1 deprecation. `null` still raises
  `InvalidDataException`, so `catch` blocks are unaffected, but the message changes from
  `Provided date format is invalid` to `Date must be provided` — the old text blamed the
  configured format for what is really a missing input. Consumers that match on
  `getMessage()` rather than the exception type, such as API error-mapping layers, need to
  account for the new string.
- Narrowed the `phpunit/phpunit` development requirement to `^9.3` — the version that introduced
  the `<coverage>` configuration element used by `phpunit.xml.dist`.
- Replaced leading-backslash class references with `use` statements throughout the library —
  global classes (`ArrayIterator`, `ArrayObject`, `DateTime`, `DateTimeZone`, `Exception` and
  the SPL exceptions) and fully qualified `Paysera\...` references in docblocks. No behaviour
  change.
- Replaced long array syntax (`array(...)`) with short syntax (`[...]`) throughout the library.
  No behaviour change.

### Removed
- Dropped support for PHP 7.1, 7.2 and 7.3. Minimum supported version is now PHP 7.4. Projects
  still on those versions resolve to 3.4.x and are unaffected.

### Fixed
- `Result::getIterator()` is marked `#[\ReturnTypeWillChange]`, silencing the PHP 8.1 tentative
  return type deprecation without changing the signature. The native `\Traversable` return type
  is deferred to 4.0.0, where it will be batched with the other type additions.
- `CamelCaseToSnakeCaseConverter::convert()` no longer passes `null` to `preg_replace()`,
  resolving a PHP 8.1 deprecation. Passing `null` still returns an empty string as before.
- `FollowUpFilter` no longer redeclares `$offset` without an initialiser. The shadowing
  declaration gave it a `null` default where `Filter` declares `0`; it now inherits the parent
  default. Instances built through the constructor were always assigned an offset there and are
  unaffected.
- `Result::$items` now defaults to an empty array, so iterating a `Result` whose items were never
  set no longer raises a `TypeError` on PHP 8 (an `InvalidArgumentException` on PHP 7.4) and
  `getItems()` honours its documented `@return mixed[]`. The default lives on the property
  declaration rather than in the constructor, so it also applies to instances built without one —
  `ReflectionClass::newInstanceWithoutConstructor()`, and the ORM hydration and mocking that build
  on it.

## 3.4.0
### Added
- PHP 8.4 support, removed implicitly nullable parameter declarations.

## 3.3.0
### Added
- Symfony 6 support.
- Added GitHub workflow

## 3.2.0
### Added
- Symfony 5 support.
### Changed
- Minor fixes

## 3.1.0
### Added
- Added PHP 8.0 support.

## 3.0.0
### Changed
- Dropped Symfony 2 support. Added Symfony 4 support. Now it supports versions 3 and 4.

### Fixed
- `PHPUnit\Framework\TestCase` implementation with new PHP Unit framework version.
- `setUp` method implementation.
- Use `expectEception` in `testMapToEntityThrowException` test.

### Added
- `.phpunit.result.cache` to `.gitignore` generated from the new version of PHP Unit.

### Removed
- `syntaxCheck` property from `phpunit.xml.dist` file.

## 2.1.0
### Added
- Added `ContextAwareDenormalizerInterface` which supports denormalization with optional `NormalizationContextInterface`
context entity.

## 2.0.0
### Removed
- Removed `Paysera\Component\Serializer\Exception\InvalidDataException` `setCodes` and `getCodes` methods,
  added `getViolations`, `setViolations` and `addViolation` instead.
