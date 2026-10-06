<?php

use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\Exceptions\StackLevelException;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Template;
use Keepsuit\Liquid\TemplatesCache\MemoryTemplatesCache;
use Keepsuit\Liquid\Tests\Stubs\StubFileSystem;

test('dynamically choosen templates are not allowed', function () {
    expect(fn () => renderTemplate("{% assign name = 'snippet' %}{% render name %}"))
        ->toThrow(SyntaxException::class);
});

test('render with filters on template name is invalid', function () {
    expect(fn () => parseTemplate('{% render "snippet" | upcase %}'))
        ->toThrow(SyntaxException::class);
});

test('render invalid trailing syntax fails during parse', function () {
    expect(fn () => parseTemplate('{% render "snippet", one: 1, two %}'))
        ->toThrow(SyntaxException::class);
});

describe('rendering with template backends', function () {
    test('render with no arguments', function (bool $compiled) {
        assertTemplateResult(
            'rendered content',
            '{% render "source" %}',
            partials: ['source' => 'rendered content'],
            compiled: $compiled);
    });

    test('render for accepts ranges with inherited strict options', function (bool $compiled, string $source, string $expected) {
        $factory = EnvironmentFactory::new()->setStrictFilters(true)->setLazyParsing(false);
        assertTemplateResult($expected, $source, partials: ['p' => '{{ i }}'], strictVariables: true, factory: $factory, compiled: $compiled);
        expect(implode('', iterator_to_array(streamTemplate($source, partials: ['p' => '{{ i }}'], strictVariables: true, factory: $factory, compiled: $compiled))))
            ->toBe($expected);
    })->with([
        'ascending' => ["{% render 'p' for (1..3) as i %}", '123'],
        'descending' => ["{% render 'p' for (3..1) as i %}", ''],
        'assigned' => ["{% assign items = (1..3) %}{% render 'p' for items as i %}", '123'],
    ]);

    test('render for over a non-iterable renders the partial once', function (bool $compiled, array $data, string $expected) {
        assertTemplateResult($expected, "{% render 'p' for v as i %}", data: $data, partials: ['p' => '<{{ i }}>'], compiled: $compiled);
        expect(implode('', iterator_to_array(streamTemplate("{% render 'p' for v as i %}", data: $data, partials: ['p' => '<{{ i }}>'], compiled: $compiled))))->toBe($expected);
    })->with([
        'string' => [['v' => 'abc'], '<abc>'],
        'number' => [['v' => 5], '<5>'],
        'nil' => [['v' => null], '<>'],
    ]);

    test('render for reports missing variables in strict variables mode', function (bool $compiled) {
        expect(fn () => renderTemplate("{% render 'p' for missing as i %}", partials: ['p' => '{{ i }}'], strictVariables: true, compiled: $compiled))
            ->toThrow(\Keepsuit\Liquid\Exceptions\UndefinedVariableException::class);
    });

    test('render passes named arguments into inner scope', function (bool $compiled) {
        assertTemplateResult(
            'My Product',
            '{% render "product", inner_product: outer_product %}',
            staticData: ['outer_product' => ['title' => 'My Product']],
            partials: ['product' => '{{ inner_product.title }}'],
            compiled: $compiled);
    });

    test('render passes parent variable as named arguments into inner scope', function (bool $compiled) {
        assertTemplateResult(
            'My Product',
            '{% render "product", product: a %}',
            data: ['a' => ['title' => 'My Product']],
            partials: ['product' => '{{ product.title }}'],
            compiled: $compiled);
    });

    test('render accepts literals as arguments', function (bool $compiled) {
        assertTemplateResult(
            '123',
            '{% render "snippet", price: 123 %}',
            partials: ['snippet' => '{{ price }}'],
            compiled: $compiled);
    });

    test('render accepts multiple named arguments', function (bool $compiled) {
        assertTemplateResult(
            '1 2',
            '{% render "snippet", one: 1, two: 2 %}',
            partials: ['snippet' => '{{ one }} {{ two }}'],
            compiled: $compiled);
    });

    test('render accepts multiple named arguments without commas', function (bool $compiled) {
        assertTemplateResult(
            '1 2',
            '{% render "snippet" one: 1 two: 2 %}',
            partials: ['snippet' => '{{ one }} {{ two }}'],
            compiled: $compiled);
    });

    test('render accepts optional commas around with alias and named arguments', function (bool $compiled) {
        assertTemplateResult(
            'Product: Draft 151cm override',
            "{% render 'product', with products[0], as item, note: 'override' %}",
            staticData: [
                'products' => [['title' => 'Draft 151cm'], ['title' => 'Element 155cm']],
            ],
            partials: [
                'product' => 'Product: {{ item.title }} {{ note }}',
            ],
            compiled: $compiled);
    });

    test('render named arguments override with value', function (bool $compiled) {
        assertTemplateResult(
            'Element 155cm',
            "{% render 'product' with products[0], product: products[1] %}",
            staticData: [
                'products' => [['title' => 'Draft 151cm'], ['title' => 'Element 155cm']],
            ],
            partials: [
                'product' => '{{ product.title }}',
            ],
            compiled: $compiled);
    });

    test('render does not inherit parent scope variables', function (bool $compiled) {
        assertTemplateResult(
            '',
            '{% assign outer_variable = "should not be visible" %}{% render "snippet" %}',
            partials: ['snippet' => '{{ outer_variable }}'],
            compiled: $compiled);
    });

    test('render does not mutate parent scope', function (bool $compiled) {
        assertTemplateResult(
            '',
            "{% render 'snippet' %}{{ inner }}",
            partials: ['snippet' => '{% assign inner = 1 %}'],
            compiled: $compiled);
    });

    test('nested render tag', function (bool $compiled) {
        assertTemplateResult(
            'one two',
            "{% render 'one' %}",
            partials: [
                'one' => "one {% render 'two' %}",
                'two' => 'two',
            ],
            compiled: $compiled);
    });

    test('recursively rendered template does not produce endless loop', function (bool $compiled) {
        expect(fn () => renderTemplate('{% render "loop" %}', partials: ['loop' => '{% render "loop" %}'], compiled: $compiled))
            ->toThrow(StackLevelException::class);
    });

    test('render tag caches second read of some partial', function (bool $compiled) {
        $environment = testEnvironmentFactory($compiled)
            ->setFilesystem($fileSystem = new StubFileSystem(['snippet' => 'echo']))->build();

        $template = testParseString($environment, '{% render "snippet" %}{% render "snippet" %}');

        expect($template->render($environment->newRenderContext()))->toBe('echoecho');
        expect($fileSystem->fileReadCount)->toBe(1);
        expect($template->render($environment->newRenderContext()))->toBe('echoecho');
        expect($fileSystem->fileReadCount)->toBe(1);
    });

    test('render tag does cache partials across parsing', function (bool $compiled) {
        $environment = testEnvironmentFactory($compiled)
            ->setFilesystem($fileSystem = new StubFileSystem(['snippet' => 'my message']))->build();

        $template = testParseString($environment, '{% render "snippet" %}');
        expect($template)
            ->state->partials->toBe(['snippet'])
            ->render($environment->newRenderContext())->toBe('my message');
        expect($fileSystem->fileReadCount)->toBe(1);
        expect($environment->templatesCache->has('snippet'))->toBeTrue();

        $template = testParseString($environment, '{% render "snippet" %}');
        expect($template)
            ->state->partials->toBe(['snippet'])
            ->render($environment->newRenderContext())->toBe('my message');
        expect($fileSystem->fileReadCount)->toBe(1);
        expect($environment->templatesCache->has('snippet'))->toBeTrue();
    });

    test('render tag checks the cache before parsing and after storing a missing partial', function (bool $compiled) {
        $cache = new class extends MemoryTemplatesCache
        {
            public int $reads = 0;

            public function get(string $name): ?Template
            {
                $this->reads++;

                return parent::get($name);
            }
        };

        $environment = testEnvironmentFactory($compiled, cache: $cache)
            ->setFilesystem(new StubFileSystem(['snippet' => 'my message']))
            ->build();

        testParseString($environment, '{% render "snippet" %}');

        expect($cache->reads)->toBe(2);
    });

    test('render tag within if statement', function (bool $compiled) {
        assertTemplateResult(
            'my message',
            '{% if true %}{% render "snippet" %}{% endif %}',
            partials: ['snippet' => 'my message'],
            compiled: $compiled);
    });

    test('break through render', function (bool $compiled) {
        assertTemplateResult(
            '1',
            '{% for i in (1..3) %}{{ i }}{% break %}{{ i }}{% endfor %}',
            partials: ['break' => '{% break %}'],
            compiled: $compiled);
        assertTemplateResult(
            '112233',
            '{% for i in (1..3) %}{{ i }}{% render "break" %}{{ i }}{% endfor %}',
            partials: ['break' => '{% break %}'],
            compiled: $compiled);
    });

    test('increment is isolated between renders', function (bool $compiled) {
        assertTemplateResult(
            '010',
            '{% increment a %}{% increment a %}{% render "incr" %}',
            partials: ['incr' => '{% increment a %}'],
            compiled: $compiled);
    });

    test('decrement is isolated between renders', function (bool $compiled) {
        assertTemplateResult(
            '-1-2-1',
            '{% decrement a %}{% decrement a %}{% render "decr" %}',
            partials: ['decr' => '{% decrement a %}'],
            compiled: $compiled);
    });

    test('render tag with', function (bool $compiled) {
        assertTemplateResult(
            'Product: Draft 151cm ',
            "{% render 'product' with products[0] %}",
            staticData: [
                'products' => [['title' => 'Draft 151cm'], ['title' => 'Element 155cm']],
            ],
            partials: [
                'product' => 'Product: {{ product.title }} ',
            ],
            compiled: $compiled);
    });

    test('render tag with alias', function (bool $compiled) {
        assertTemplateResult(
            'Product: Draft 151cm ',
            "{% render 'product_alias' with products[0] as product %}",
            staticData: [
                'products' => [['title' => 'Draft 151cm'], ['title' => 'Element 155cm']],
            ],
            partials: [
                'product_alias' => 'Product: {{ product.title }} ',
            ],
            compiled: $compiled);
    });

    test('render tag for', function (bool $compiled) {
        assertTemplateResult(
            'Product: Draft 151cm Product: Element 155cm ',
            "{% render 'product' for products %}",
            staticData: [
                'products' => [['title' => 'Draft 151cm'], ['title' => 'Element 155cm']],
            ],
            partials: [
                'product' => 'Product: {{ product.title }} ',
            ],
            compiled: $compiled);
    });

    test('render tag for alias', function (bool $compiled) {
        assertTemplateResult(
            'Product: Draft 151cm Product: Element 155cm ',
            "{% render 'product_alias' for products as product %}",
            staticData: [
                'products' => [['title' => 'Draft 151cm'], ['title' => 'Element 155cm']],
            ],
            partials: [
                'product_alias' => 'Product: {{ product.title }} ',
            ],
            compiled: $compiled);
    });

    test('render tag forloop', function (bool $compiled) {
        assertTemplateResult(
            'Product: Draft 151cm first  index:1 Product: Element 155cm  last index:2 ',
            "{% render 'product' for products %}",
            staticData: [
                'products' => [['title' => 'Draft 151cm'], ['title' => 'Element 155cm']],
            ],
            partials: [
                'product' => 'Product: {{ product.title }} {% if forloop.first %}first{% endif %} {% if forloop.last %}last{% endif %} index:{{ forloop.index }} ',
            ],
            compiled: $compiled);
    });

    test('render tag for drop', function (bool $compiled) {
        assertTemplateResult(
            '123',
            "{% render 'loop' for iterator as value %}",
            staticData: [
                'iterator' => new \Keepsuit\Liquid\Tests\Stubs\IteratorDrop,
            ],
            partials: [
                'loop' => '{{ value.foo }}',
            ],
            compiled: $compiled);
    });

    test('render tag with drop', function (bool $compiled) {
        assertTemplateResult(
            '1',
            "{% render 'loop' with data as value %}",
            staticData: [
                'data' => 1,
            ],
            partials: [
                'loop' => '{{ value }}',
            ],
            compiled: $compiled);
    });

    test('render tag renders error with template name', function (bool $compiled) {
        assertTemplateResult(
            'Liquid error (foo line 1): Standard error',
            "{% render 'foo' with errors %}",
            staticData: [
                'errors' => new \Keepsuit\Liquid\Tests\Stubs\ErrorDrop,
            ],
            partials: [
                'foo' => '{{ foo.standard_error }}',
            ],
            renderErrors: true, compiled: $compiled);
    });

    test('render stream', function (bool $compiled) {
        $stream = streamTemplate(
            "{% render 'product' for products %}",
            staticData: [
                'products' => [['title' => 'Draft 151cm'], ['title' => 'Element 155cm']],
            ],
            partials: [
                'product' => 'Product: {{ product.title }} ',
            ],
            compiled: $compiled);

        $output = iterator_to_array($stream);

        // Compiled fallback tags can yield different chunk boundaries.
        expect(implode('', $output))
            ->toBe('Product: Draft 151cm Product: Element 155cm ');
    });
})->with('template backends');
