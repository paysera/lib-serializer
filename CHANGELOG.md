# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## 2.1.2
### Added
- Added `ContextAwareDenormalizerInterface` which supports denormalization with optional `NormalizationContextInterface`
context entity.

## 2.0.0
### Removed
- Removed `Paysera\Component\Serializer\Exception\InvalidDataException` `setCodes` and `getCodes` methods,
  added `getViolations`, `setViolations` and `addViolation` instead.
