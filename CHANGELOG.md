# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## 3.6.0
### Added
- Symfony 7 support: `symfony/property-access` and `symfony/validator` also allow `^7.4`, the
  long-term-support release of Symfony 7 (7.0 to 7.3 are no longer maintained). On Symfony 7 a
  constraint's error name is read only from its `ERROR_NAMES` constant, so a custom constraint
  that names its errors only in the `$errorNames` property is reported by
  `PropertiesAwareValidator` with its raw code. Declare `protected const ERROR_NAMES`, and keep
  `$errorNames` next to it while you support Symfony below 6.1, which reads only the property
  (6.1 to 6.4 read both).
- Tests for every class. `PropertiesAwareValidator` and `PropertyPathFieldAccessor` are tested
  against Symfony's real validator and property accessor, so each Symfony version in the test
  matrix runs the code that calls Symfony.

### Removed
- `symfony/validator` 3.0.x, 3.1.0 to 3.1.8 and 3.2.0 to 3.2.1. When a validated property holds
  an array or an object under `Valid`, they call `count()` on `null`, which PHP 7.4 reports as a
  warning; on PHP 8 the older of them do not compile at all and the rest throw a `TypeError`.
  Projects locked to one of them stay on 3.5.x.

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
