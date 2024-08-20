# Changelog

All notable changes to `liquid` will be documented in this file.

## v0.6.4 - 2024-08-20

### What's Changed

* Support objects in `where` filter
* Bump dependabot/fetch-metadata from 2.1.0 to 2.2.0 by @dependabot in https://github.com/keepsuit/php-liquid/pull/27
* Update custom filters example in README.md by @tillschander in https://github.com/keepsuit/php-liquid/pull/26

### New Contributors

* @tillschander made their first contribution in https://github.com/keepsuit/php-liquid/pull/26

**Full Changelog**: https://github.com/keepsuit/php-liquid/compare/v0.6.3...v0.6.4

## v0.6.3 - 2024-05-30

### What's Changed

* Improved iterable support by @cappuc in https://github.com/keepsuit/php-liquid/pull/23

**Full Changelog**: https://github.com/keepsuit/php-liquid/compare/v0.6.2...v0.6.3

## v0.6.2 - 2024-05-27

### What's Changed

* pass filter arguments as array by @cappuc in https://github.com/keepsuit/php-liquid/pull/22

**Full Changelog**: https://github.com/keepsuit/php-liquid/compare/v0.6.1...v0.6.2

## v0.6.1 - 2024-05-25

### What's Changed

* Strict variables improvements by @cappuc in https://github.com/keepsuit/php-liquid/pull/21

**Full Changelog**: https://github.com/keepsuit/php-liquid/compare/v0.6.0...v0.6.1

## v0.6.0 - 2024-05-10

### What's Changed

* Rename template factory setter methods and add getters by @cappuc in https://github.com/keepsuit/php-liquid/pull/19
* Nested variable override by @cappuc in https://github.com/keepsuit/php-liquid/pull/18
* Added toArray method to drop by @cappuc in https://github.com/keepsuit/php-liquid/pull/20
* Bump aglipanci/laravel-pint-action from 2.3.1 to 2.4 by @dependabot in https://github.com/keepsuit/php-liquid/pull/15
* Bump dependabot/fetch-metadata from 2.0.0 to 2.1.0 by @dependabot in https://github.com/keepsuit/php-liquid/pull/16

**Full Changelog**: https://github.com/keepsuit/php-liquid/compare/v0.5.3...v0.6.0

## v0.5.3 - 2024-04-04

### What's changed

- Allow access of `Drop` public properties with snake case and camel case aliases
- Allow to hide a public property of `Drop` with `Hidden` attribute

**Full Changelog**: https://github.com/keepsuit/php-liquid/compare/v0.5.2...v0.5.3

## v0.5.2 - 2024-03-30

### What's Changed

* Make `TemplateFactory` settings public readable
* Bump dependabot/fetch-metadata from 1.6.0 to 2.0.0 by @dependabot in https://github.com/keepsuit/php-liquid/pull/14

**Full Changelog**: https://github.com/keepsuit/php-liquid/compare/v0.5.1...v0.5.2

## v0.5.1 - 2024-03-19

### What's changed

* Don't preserve keys when transforming generator to array (it can break with `yield from`)

**Full Changelog**: https://github.com/keepsuit/php-liquid/compare/v0.5.0...v0.5.1

## v0.5.0 - 2024-03-06

### Breaking changes

* Added `AsLiquidValue` interface to provide a scalar value from objects used for comparisons
* Added `Hidden` and `Cache` attributes for drop methods
* Rewritten and improved drops metadata extraction
* Improved `RenderContext` values caching

### What's Changed

* added cacheable methods to drop by @cappuc in https://github.com/keepsuit/php-liquid/pull/12
* extract drop metadata only once per drop class by @cappuc in https://github.com/keepsuit/php-liquid/pull/13

**Full Changelog**: https://github.com/keepsuit/php-liquid/compare/v0.4.2...v0.5.0

## v0.4.2 - 2024-02-01

### What's changed

* Fixed filesystem replace (introduced in v0.4.1)
* Don't replace exception `templateName` and `lineNumber` when provided

**Full Changelog**: https://github.com/keepsuit/php-liquid/compare/v0.4.1...v0.4.2

## v0.4.1 - 2024-02-01

### What's changed

* Make `rethrowExceptions` and `filterRegistry` public accessible in `RenderContext`

**Full Changelog**: https://github.com/keepsuit/php-liquid/compare/v0.4.0...v0.4.1

## v0.4.0 - 2024-01-31

### Breaking changes

* Lexer and Parser were rewritten from scratch so custom tags needs to be updated.
* Removed line numbers settings since they are always tracked now.
* Removed raw markup from nodes.
* Removed `I18n` locale provider (it was only used for exception messages, now they are always in english).
* Renamed `Context` to `RenderContext`.
* Removed `outerScope` initialization in `RenderContext`
* Removed the starting `_` in `LocalFileSystem` pattern for searching liquid files.
* Support dot syntax in `LocalFileSystem` resolver (`dir.template` become `dir/template.liquid`)
* Introduced `PartialsCache` and `OutputsBag` used to shared state between parsing and rendering (for compiled child templates and general outputs)
* Added experimental template streaming support (with generators)

### What's Changed

* Bump actions/checkout from 3 to 4 by @dependabot in https://github.com/keepsuit/php-liquid/pull/5
* Bump stefanzweifel/git-auto-commit-action from 4 to 5 by @dependabot in https://github.com/keepsuit/php-liquid/pull/7
* Bump aglipanci/laravel-pint-action from 2.3.0 to 2.3.1 by @dependabot in https://github.com/keepsuit/php-liquid/pull/8
* Bump actions/cache from 3 to 4 by @dependabot in https://github.com/keepsuit/php-liquid/pull/9
* Refactoring by @cappuc in https://github.com/keepsuit/php-liquid/pull/10
* Streaming by @cappuc in https://github.com/keepsuit/php-liquid/pull/11

**Full Changelog**: https://github.com/keepsuit/php-liquid/compare/v0.3.2...v0.4.0

## v0.3.2 - 2023-09-04

### What's changed

- Fallback to null on parser look up to undefined index

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
