# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## 3.0.0
### Changed
```
"php": "^7.1.3",
"phpunit/phpunit": "^7.0"
"symfony/property-access": "^4.0",
"symfony/validator": "^4.0",`
```

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
