# PHP implementation of Liquid markup language

[![Latest Version on Packagist](https://img.shields.io/packagist/v/keepsuit/liquid.svg?style=flat-square)](https://packagist.org/packages/keepsuit/liquid)
[![Tests](https://img.shields.io/github/actions/workflow/status/keepsuit/liquid/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/keepsuit/liquid/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/keepsuit/liquid.svg?style=flat-square)](https://packagist.org/packages/keepsuit/liquid)

This is a PHP porting of the [Shopify Liquid template engine](https://github.com/Shopify/liquid).

Liquid is a template engine with interesting advantages:

- It is easy to learn and has a simple syntax.
- It is safe since it does not allow users to run insecure code on your server.
- It is extensible, allowing you to add your own filters and tags.

## Shopify Liquid version compatibility

| Shopify Liquid | PHP Liquid |
|---------------:|-----------:|
|            5.4 |        0.1 |

#### Differences from Shopify Liquid

- **Error Modes** are not implemented, the parsing is always strict.
- `include` tag is not implemented because it is deprecated and can be replaced with `render`.

## Installation

You can install the package via composer:

```bash
composer require keepsuit/liquid
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Shopify](https://github.com/Shopify/liquid)
- [Fabio Capucci](https://github.com/cappuc)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
