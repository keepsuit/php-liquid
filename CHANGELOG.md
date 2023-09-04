# Changelog

All notable changes to `liquid` will be documented in this file.

## v0.3.1 - 2023-09-04

### What's changed

- Improved lexer/parser with start position of tokens
- Refactoring Variable parsing

## v0.3.0 - 2023-09-01

### What's Changed

- Removed ParseContext from tag state, so its no longer available during render phase by @cappuc in https://github.com/keepsuit/php-liquid/pull/3
- Update render tag to parse partial template in the parse phase instead of render phase by @cappuc in https://github.com/keepsuit/php-liquid/pull/3
- Share partials cache between parse context and render context (saving them in the root Template after parsing) by @cappuc in https://github.com/keepsuit/php-liquid/pull/3
- Renamed parse method of TemplateFactory to parseString and added parseTemplate method (it parses a template from filesystem) by @cappuc in https://github.com/keepsuit/php-liquid/pull/3

**Full Changelog**: https://github.com/keepsuit/php-liquid/compare/v0.2.1...v0.3.0

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
