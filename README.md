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

|    PHP Liquid | Shopify Liquid |
| ------------: | -------------: |
| v0.11 - v0.12 |          v5.13 |
|         v0.10 |          v5.12 |
|          v0.9 |           v5.8 |
|          v0.8 |           v5.7 |
|          v0.7 |           v5.6 |
|   v0.1 - v0.6 |           v5.5 |

#### Differences from Shopify Liquid

- **Error Modes** are not implemented, the parsing is always strict.
  Invalid syntax raises `SyntaxException` when parsed, independently of `strictVariables`,
  `strictFilters`, and `rethrowErrors`. These options control rendering errors.
  `lazyParsing` permits loading uncached partials during rendering; their syntax is still
  parsed strictly, and errors then follow the rendering error handler or `rethrowErrors`.
- `case` deliberately renders only the first matching `when`. A `case` block
  can contain at most one `else`, after its `when` sections; `else` before a
  `when` or a `when` after `else` is a syntax error.
- `size`, `first` and `last` work with both dot and bracket notation (`a.first` and `a["first"]`);
  Shopify Liquid supports them only with dot notation.
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
    // enable strict variables mode (disabled by default)
    ->setStrictVariables(true)
    // enable strict filters mode (disabled by default)
    ->setStrictFilters(true)
    // rethrow exceptions instead of rendering them (disabled by default)
    ->setRethrowErrors(true)
    // disable lazy parsing (enabled by default)
    ->setLazyParsing(false)
    // replace the default error handler
    ->setErrorHandler(new \Keepsuit\Liquid\ErrorHandlers\DefaultErrorHandler())
    // set filesystem used to load templates
    ->setFilesystem(new \Keepsuit\Liquid\FileSystems\LocalFileSystem(__DIR__ . '/views'))
    // set the resource limits
    ->setResourceLimits(new \Keepsuit\Liquid\Render\ResourceLimits(
        renderLengthLimit: 100_000,
        renderScoreLimit: 50_000,
        assignScoreLimit: 5_000,
        cumulativeRenderScoreLimit: 100_000,
        cumulativeAssignScoreLimit: 10_000,
    ))
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

### Output formatting

Float output follows Shopify Liquid 5.13: integral floats keep `.0` (`{{ 2 | times: 1.5 }}` renders
`3.0`), other floats use the shortest decimal representation that round-trips, with Ruby-style
scientific notation, `-0.0`, `Infinity`, `-Infinity` and `NaN`. Formatting relies on PHP's default `serialize_precision` (`-1`) and
does not depend on the numeric locale. Like Shopify, `plus`, `minus`, `times`, `divided_by`,
`modulo` and `sum` compute in decimal arithmetic on floats' shortest representation:
`{{ 0.0725 | times: 100 }}` renders `7.25`.

`tablerow` follows Shopify's HTML whitespace: a newline after the first `<tr>`, adjacent cells,
`\n` between rows and after the final `</tr>`. Whitespace-control markers on `raw` delimiters trim
only the surrounding text: `{%- raw -%} a {%- endraw -%}` renders ` a `.

**Compatibility change:** float strings and `tablerow` HTML differ from earlier releases. Prices,
string conversions and exact-output snapshots may need updating.

### Date formatting

The `date` filter uses Ruby-style strftime directives: only `%` directives are formatted, other text is literal
(`date: 'Day %d at %H'` produces `Day 05 at 14`). Day and month names are always English.
Values that cannot be parsed as a date (including floats and booleans) are returned unchanged, without errors.

### Numeric filters

Standard numeric filters use Liquid coercion: `nil`, booleans and non-numeric strings become zero;
strings can supply a leading integer prefix (`'1abc' | plus: 1` produces `2`).
Integer division rounds down, and modulo follows the divisor's sign, including for floats.
Float division by zero produces `Infinity`, `-Infinity` or `NaN`; integer division by zero remains a Liquid error.

These conversions also apply with `strictVariables` and `strictFilters` enabled. Missing variables still
report an error under `strictVariables`; `strictFilters` controls unknown filter names.
`rethrowErrors` controls error propagation, and `lazyParsing` controls loading partials during rendering.
Custom filters retain their own parameter types. Arithmetic uses PHP's native integer and float precision.

### String and HTML filters

Standard string and HTML filters use the shared Liquid string coercion: booleans become `true`/`false`
and `nil` becomes an empty string. Supplied string arguments use the same conversion. Optional arguments
follow the filter's defaults (`replace` and `replace_first` default to an empty replacement);
required arguments remain required. Integer arguments use Liquid integer validation.

`split` with `' '` splits whitespace runs, with `''` splits multibyte characters, and otherwise drops
trailing empty fields. `slice` accepts an offset of `-size`. `truncatewords` ignores leading whitespace
when counting words and preserves the original input when no truncation is needed.

`escape`, its alias `h`, and `escape_once` encode only `& < > " '`, using `&#39;` for apostrophes.
`strip_html` removes multiline script/style blocks and comments. URL-safe Base64 filters are available;
encoding includes padding, while decoding accepts omitted padding and rejects malformed encodings.
Standard Base64 decoding requires correctly padded input.

These conversions also apply with strict options enabled. Missing variables still report errors under
`strictVariables`, unknown filters follow `strictFilters`, and `rethrowErrors` controls propagation.
`lazyParsing` only controls partial loading. Custom filters retain their own parameter types.
The shared string coercion supports scalars and `Stringable` objects; arrays and other objects become
empty strings rather than Ruby-style representations.

### Array filters

Standard array filters accept `nil`, scalars, lists, hashes, and iterators. Filters using a collection
flatten nested PHP lists, preserve associative arrays as hashes, and bind drops to the current context.
An empty PHP array is a list, so nested empty arrays disappear during flattening. Iterator entries
retain their structure. `first`, `last`, and `size` preserve the original collection shape;
`first` on a hash returns its first key/value pair, while `last` returns `nil`.
Hash lookups retain their existing key-based behavior; use the explicit `first` filter for a pair.
On integers, `size` returns the native integer byte size, as in Ruby.

`map` retains arrays returned by properties; a subsequent `join` flattens them.
`concat` flattens its input but keeps the appended array's structure and requires an array argument.
`join` uses Liquid string conversion, including boolean names, float types, and Ruby-style hash
representations. `sum` uses the shared numeric coercion, including leading numeric prefixes.

`uniq` preserves the first occurrence and distinguishes integers, floats, strings, and booleans.
Hashes compare by content regardless of key order, and drops compare by identity unless they expose
a Liquid value. `compact` removes only `nil`. Without a target, `where`, `reject`, `has`, `find`,
and `find_index` treat only `nil` and `false` as falsy; with a target they use condition equality.
`sort` rejects incompatible types with a Liquid argument error; `sort_natural` compares strings
case-insensitively.

These rules apply with strict options enabled. Missing variables still follow `strictVariables`,
unknown filters follow `strictFilters`, and rendering errors follow the configured handler or
`rethrowErrors`. `lazyParsing` only controls partial loading, and parsing remains strict.
Custom filters retain their parameter types.

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

### Templates cache

By default compiled templates are kept in a `MemoryTemplatesCache`, which lasts as long as the PHP process, so with PHP-FPM every request parses the templates again.
`SerializeTemplatesCache` and `VarExportTemplatesCache` store compiled templates on disk.
With `keepInMemory: true` (the default) each template is read from disk only once per cache instance.

```php
$environment = \Keepsuit\Liquid\EnvironmentFactory::new()
    ->setTemplatesCache(new \Keepsuit\Liquid\TemplatesCache\SerializeTemplatesCache(__DIR__.'/cache/liquid'))
    ->build();
```

`VarExportTemplatesCache` requires `symfony/var-exporter`.

## Resource limits

`Keepsuit\Liquid\Render\ResourceLimits` supports both per-render limits and cumulative resource limits.

- `renderLengthLimit`: limits the rendered output size for a render pass.
- `renderScoreLimit`: limits render work for a render pass.
- `assignScoreLimit`: limits assignment and capture work for a render pass.
- `cumulativeRenderScoreLimit`: limits total render work across a full render tree, including partial renders.
- `cumulativeAssignScoreLimit`: limits total assignment and capture work across a full render tree, including partial renders.

Per-render counters can be reset between renders. Cumulative counters are intended for a full render lifecycle so repeated partial renders can share the same budget.

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

This runs the full suite twice, using in-memory and compiled templates with the same
expected outputs. Rendering helpers compile roots and partials, including partials
loaded lazily. Parser, AST and serialized-cache tests retain their parsed fixtures;
compiler-specific tests continue to exercise their explicit backends. Streaming
output comparisons concatenate chunks when their boundaries are not part of the test.

To run a single backend:

```bash
composer test:in-memory
composer test:compiled
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Shopify](https://github.com/Shopify/liquid)
- [Fabio Capucci](https://github.com/cappuc)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
