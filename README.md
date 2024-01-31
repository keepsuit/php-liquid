# PHP implementation of Liquid markup language

[![Latest Version on Packagist](https://img.shields.io/packagist/v/keepsuit/liquid.svg?style=flat-square)](https://packagist.org/packages/keepsuit/liquid)
[![Tests](https://img.shields.io/github/actions/workflow/status/keepsuit/php-liquid/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/keepsuit/liquid/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/keepsuit/liquid.svg?style=flat-square)](https://packagist.org/packages/keepsuit/liquid)

This is a PHP porting of the [Shopify Liquid template engine](https://github.com/Shopify/liquid).

If you are using laravel, you can use the [laravel-liquid](https://github.com/keepsuit/laravel-liquid) package.

Liquid is a template engine with interesting advantages:

- It is easy to learn and has a simple syntax.
- It is safe since it does not allow users to run insecure code on your server.
- It is extensible, allowing you to add your own filters and tags.

## Shopify Liquid version compatibility

| Shopify Liquid | PHP Liquid |
|---------------:|-----------:|
|            5.4 |        0.4 |

#### Differences from Shopify Liquid

- **Error Modes** are not implemented, the parsing is always strict.
- `include` tag is not implemented because it is deprecated and can be replaced with `render`.

## Installation

You can install the package via composer:

```bash
composer require keepsuit/liquid
```

## Usage

Create a new template factory instance:

```php
$factory = \Keepsuit\Liquid\TemplateFactory::new()
    // enable strict variables mode
    ->strictVariables()
    // rethrow exceptions instead of rendering them
    ->rethrowExceptions()
    // set filesystem used to load templates
    ->setFilesystem(new \Keepsuit\Liquid\FileSystems\LocalFileSystem(__DIR__ . '/views'));
```

Then create a new template instance parsing a liquid template:

```php
/** @var \Keepsuit\Liquid\TemplateFactory $factory */

// Parse from string
$template = $factory->parseString('Hello {{ name }}!');

// Parse from template (loaded from filesystem)
$template = $factory->parseTemplate('index');
```

And finally render the template:

```php
/** @var \Keepsuit\Liquid\TemplateFactory $factory */
/** @var \Keepsuit\Liquid\Template $template */

// Create the render context
$context = $factory->newRenderContext(
    // Environment variables only available in the current context
    environment: [
        'name' => 'John',
    ],
    // Environment variables that are shared with all sub-contexts
    staticEnvironment: []
)

$view = $template->render($context);
// $view = 'Hello John!';
```

For advanced use cases, you can also stream the rendering output (still experimental):

```php
/** @var \Keepsuit\Liquid\Template $template */
/** @var \Keepsuit\Liquid\Render\RenderContext $context */

$stream = $template->stream($context);
// $stream is a Generator<string>
```

## Advanced usage

### Custom tags

To create a custom tag, you need to create a class that extends the `Keepsuit\Liquid\Tag` abstract class (or `Keepsuit\Liquid\TagBlock` if tag has a body).

```php
use Keepsuit\Liquid\Parse\TagParseContext;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Tag;

class CustomTag extends Tag
{
    public static function tagName(): string
    {
        return 'custom';
    }

    public function render(RenderContext $context): string
    {
        return '';
    }

    public function parse(TagParseContext $context): static
    {
        return $this;
    }
}

```

> [!NOTE]
> Take a look at the implementation of default tags to see how to implement `parse` and `render` methods.

Then you need to register the tag in the template factory:

```php
/** @var \Keepsuit\Liquid\TemplateFactory $factory */

$factory->registerTag(CustomTag::class);
```

### Custom filters

To create a custom filter, you need to create a class that extends the `Keepsuit\Liquid\Filters\FiltersProvider` abstract class.

Each public method of the class will be registered as a filter.

```php
use Keepsuit\Liquid\Filters\FiltersProvider;

class CustomFilters extends FiltersProvider
{
    public function customFilter(string $value): string
    {
        return 'custom '.$value;
    }
}
```

Then you need to register the filters provider in the template factory:

```php
/** @var \Keepsuit\Liquid\TemplateFactory $factory */

$factory->registerFilters(CustomFilters::class);
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
