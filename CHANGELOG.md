# Changelog

All notable changes to `liquid` will be documented in this file.

## v0.2.1 - 2023-09-01

### What's changed

- Exceptions extends `ErrorException` instead of `Exception` to provide filename and line context
- Renamed `render` method of exceptions to `toLiquidMessage` (`render` method conflicts with Laravel exception rendering)
- Updated parsing of `comment` tag in order to ignore syntax errors in body content

**Full Changelog**: https://github.com/keepsuit/php-liquid/compare/v0.2.0...v0.2.1

## v0.2.0 - 2023-08-31

### What's Changed

- Added TemplateFactory by @cappuc in https://github.com/keepsuit/php-liquid/pull/2

**Full Changelog**: https://github.com/keepsuit/php-liquid/compare/v0.1.1...v0.2.0

## v0.1.1 - 2023-08-30

### What's changed

- Removed `include` tag (only parsing was implemented)

**Full Changelog**: https://github.com/keepsuit/php-liquid/compare/v0.1.0...v0.1.1

## v0.1.0 - 2023-08-30

### Initial release

**Full Changelog**: https://github.com/keepsuit/php-liquid/commits/v0.1.0
