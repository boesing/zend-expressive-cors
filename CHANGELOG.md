# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 1.1.0 - 2019-12-30

### Added

- [#4](https://github.com/boesing/zend-expressive-cors/pull/4) PHP 7.4 support. 

### Changed

- [#5](https://github.com/boesing/zend-expressive-cors/pull/5) Upgrade to PHPUnit v8

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#3](https://github.com/boesing/zend-expressive-cors/pull/3) Avoid duplicated `Vary` header.

## 1.0.0 - 2019-11-19

Stable version `v1.0`

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.0.0rc2 - 2019-11-13

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#2](https://github.com/boesing/zend-expressive-cors/pull/2) Routes without own configuration duplicated the `allowedHeaders` configuration due to the merging process.

## 1.0.0rc1 - 2019-10-11

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#1](https://github.com/boesing/zend-expressive-cors/pull/1) Non-Preflight Request has no `Access-Control-Request-Method` header and therefore failed while creating the `CorsMetadata`.

## 0.1.0 - 2019-10-01

Initial release.

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.
