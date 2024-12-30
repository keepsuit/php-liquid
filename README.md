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

|  PHP Liquid | Shopify Liquid |
|------------:|---------------:|
|        v0.7 |           v5.6 |
| v0.1 - v0.6 |           v5.5 |

#### Differences from Shopify Liquid

- **Error Modes** are not implemented, the parsing is always strict.
- `include` tag is not implemented because it is deprecated and can be replaced with `render`.

## Installation

You can install the package via composer:

```bash
composer require keepsuit/liquid
```

## Usage

Create a new environment factory instance:

```php
$environment = \Keepsuit\Liquid\EnvironmentFactory::new()
    // enable strict variables mode
    ->setStrictVariables()
    // enable strict filters mode
    ->setStrictFilters()
    // rethrow exceptions instead of rendering them
    ->setRethrowErrors()
    // replace the default error handler
    ->setErrorHandler(new \Keepsuit\Liquid\ErrorHandlers\DefaultErrorHandler())
    // set filesystem used to load templates
    ->setFilesystem(new \Keepsuit\Liquid\FileSystems\LocalFileSystem(__DIR__ . '/views'))
    // set the resource limits
    ->setResourceLimits(new \Keepsuit\Liquid\ResourceLimits())
    // register a custom extension
    ->addExtension(new CustomExtension())
    // register a custom tag
    ->registerTag(CustomTag::class)
    // register a custom filters provider
    ->registerFilters(CustomFilters::class)
    // build the environment
    ->build();
```

Then create a new template instance parsing a liquid template:

```php
/** @var \Keepsuit\Liquid\Environment $environment */

// Parse from string
$template = $environment->parseString('Hello {{ name }}!');

// Parse from template (loaded from filesystem)
$template = $environment->parseTemplate('index');
```

And finally render the template:

```php
/** @var \Keepsuit\Liquid\Environment $environment */
/** @var \Keepsuit\Liquid\Template $template */

// Create the render context
$context = $environment->newRenderContext(
    // Data available only in the current context
    data: [
        'name' => 'John',
    ],
    // Data shared with all sub-contexts
    staticData: []
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

## Drops

Liquid support almost any kind of object but in order to have a better control over the accessible data in the templates,
you can pass your data as `Drop` objects and have a better control over the accessible data.
Drops are standard php objects that extend the `Keepsuit\Liquid\Drop` class.
Public properties and public methods of the class will be accessible in the template as a property.
You can also override the `liquidMethodMissing` method to handle undefined properties.

Liquid provides some attributes to control the behavior of the drops:
- `Hidden`: Hide the method or the property from the template, it cannot be accessed from liquid.
- `Cache`: Cache the result of the method, it will be called only once and the result will be stored in the drop.

```php
use Keepsuit\Liquid\Drop;

class ProductDrop extends Drop {
    public function __construct(private Product $product) {}

    public function title(): string {
        return $this->product->title;
    }

    public function price(): float {
        return round($this->product->price, 2);
    }
    
    #[\Keepsuit\Liquid\Attributes\Cache]
    public function expensiveOperation(){
        // complex operation
    }
    
    #[\Keepsuit\Liquid\Attributes\Hidden]
    public function buy(){
        // Do something
    }
}
```

If you implement the `MapsToLiquid` interface in your domain classes, 
the liquid renderer will automatically convert your objects to drops.

```php
use Keepsuit\Liquid\Contracts\MapsToLiquid;

class Product implements MapsToLiquid {
    public function __construct(public string $title, public float $price) {}

    public function toLiquid(): ProductDrop {
        return new ProductDrop($this);
    }
}
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

Then you need to register the tag in the environment:

```php
// register when building the environment
$environment = \Keepsuit\Liquid\EnvironmentFactory::new()
    ->registerTag(CustomTag::class)
    ->build();

// or directly in the environment
$environment->tagRegistry->register(CustomTag::class);
```

### Custom filters

To create a custom filter, you need to create a class that extends the `Keepsuit\Liquid\Filters\FiltersProvider` abstract class.

Each public method of the class will be registered as a filter. 
You can "hide" a public method with the `Hidden` attribute, so it will not be registered as filter.

```php
use Keepsuit\Liquid\Filters\FiltersProvider;

class CustomFilters extends FiltersProvider
{
    public function customFilter(string $value): string
    {
        return 'custom '.$value;
    }
    
    #[\Keepsuit\Liquid\Attributes\Hidden]
    public function notAFilter(string $value): string
    {
        return 'hidden '.$value;
    }
}
```

Then you need to register the filters provider in the environment:

```php
// register when building the environment
$environment = \Keepsuit\Liquid\EnvironmentFactory::new()
    ->registerFilters(CustomFilters::class)
    ->build();

// or directly in the environment
$environment->filterRegistry->register(CustomFilters::class);
```

### Extensions

Extensions allow you to add custom tags, filters, and other features to the liquid environment.

To create a custom extension, you need to create a class that extends the `Keepsuit\Liquid\Extensions\Extension` abstract class.

```php
class CustomExtension extends \Keepsuit\Liquid\Extensions\Extension
{
    public function getTags() : array{
        return [
            CustomTag::class,
        ];
    }
    
    public function getFiltersProviders() : array{
        return [
            CustomFilters::class,
        ];
    }
    
    // custom registers passed to render context
    public function getRegisters() : array {
        return [
            'custom' => fn() => 'custom value',
        ];
    }
}
```

Then you need to register the extension in the environment:
```php
// register when building the environment
$environment = \Keepsuit\Liquid\EnvironmentFactory::new()
    ->addExtension(new CustomExtension())
    ->build();

// or directly in the environment
$environment->addExtension(new CustomExtension());
```

## Custom tags and filters

By default, only the standard liquid tags and filters are available.
But this package provides some custom tags and filters that you can use.

### Tags

- `DynamicRender`: This tag replace the default `Render` tag and allows to render dynamic templates (eg. read template name from a variable).

### Filters

- `TernaryFilter`
  - `ternary`: adds a ternary operator.
      ```liquid
      {{ condition | ternary: true_value, false_value }}
    
      # Example
      {{ true | ternary: 'yes', 'no' }} # yes
      {{ false | ternary: 'yes', 'no' }} # no
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
