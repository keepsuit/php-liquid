<?php

use Keepsuit\Liquid\Context;
use Keepsuit\Liquid\Exceptions\StackLevelException;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Template;
use Keepsuit\Liquid\Tests\Stubs\StubFileSystem;

test('render with no arguments', function () {
    assertTemplateResult(
        'rendered content',
        '{% render "source" %}',
        partials: ['source' => 'rendered content'],
    );
});

test('render passes named arguments into inner scope', function () {
    assertTemplateResult(
        'My Product',
        '{% render "product", inner_product: outer_product %}',
        assigns: ['outer_product' => ['title' => 'My Product']],
        partials: ['product' => '{{ inner_product.title }}'],
    );
});

test('render accepts literals as arguments', function () {
    assertTemplateResult(
        '123',
        '{% render "snippet", price: 123 %}',
        partials: ['snippet' => '{{ price }}'],
    );
});

test('render accepts multiple named arguments', function () {
    assertTemplateResult(
        '1 2',
        '{% render "snippet", one: 1, two: 2 %}',
        partials: ['snippet' => '{{ one }} {{ two }}'],
    );
});

test('render does not inherit parent scope variables', function () {
    assertTemplateResult(
        '',
        '{% assign outer_variable = "should not be visible" %}{% render "snippet" %}',
        partials: ['snippet' => '{{ outer_variable }}'],
    );
});

test('render does not mutate parent scope', function () {
    assertTemplateResult(
        '',
        "{% render 'snippet' %}{{ inner }}",
        partials: ['snippet' => '{% assign inner = 1 %}'],
    );
});

test('nested render tag', function () {
    assertTemplateResult(
        'one two',
        "{% render 'one' %}",
        partials: [
            'one' => "one {% render 'two' %}",
            'two' => 'two',
        ],
    );
});

test('recursively rendered template does not produce endless loop', function () {
    expect(fn () => renderTemplate('{% render "loop" %}', partials: ['loop' => '{% render "loop" %}']))
        ->toThrow(StackLevelException::class);
});

test('dynamically choosen templates are not allowed', function () {
    expect(fn () => renderTemplate("{% assign name = 'snippet' %}{% render name %}"))
        ->toThrow(SyntaxException::class);
});

test('render tag caches second read of some partial', function () {
    $fileSystem = new StubFileSystem(['snippet' => 'echo']);

    $context = new Context(
        fileSystem: $fileSystem
    );

    expect(Template::parse('{% render "snippet" %}{% render "snippet" %}')->render($context))->toBe('echoecho');
    expect($fileSystem->fileReadCount)->toBe(1);
});

test('render tag does not cache partials across renders', function () {
    $fileSystem = new StubFileSystem(['snippet' => 'my message']);

    $context = new Context(
        fileSystem: $fileSystem
    );
    expect(Template::parse('{% render "snippet" %}')->render($context))->toBe('my message');
    expect($fileSystem->fileReadCount)->toBe(1);

    $context = new Context(
        fileSystem: $fileSystem
    );
    expect(Template::parse('{% render "snippet" %}')->render($context))->toBe('my message');
    expect($fileSystem->fileReadCount)->toBe(2);
});

test('render tag within if statement', function () {
    assertTemplateResult(
        'my message',
        '{% if true %}{% render "snippet" %}{% endif %}',
        partials: ['snippet' => 'my message'],
    );
});

test('break through render', function () {
    assertTemplateResult(
        '1',
        '{% for i in (1..3) %}{{ i }}{% break %}{{ i }}{% endfor %}',
        partials: ['break' => '{% break %}'],
    );
    assertTemplateResult(
        '112233',
        '{% for i in (1..3) %}{{ i }}{% render "break" %}{{ i }}{% endfor %}',
        partials: ['break' => '{% break %}'],
    );
});

test('increment is isolated between renders', function () {
    assertTemplateResult(
        '010',
        '{% increment %}{% increment %}{% render "incr" %}',
        partials: ['incr' => '{% increment %}'],
    );
});

test('decrement is isolated between renders', function () {
    assertTemplateResult(
        '-1-2-1',
        '{% decrement %}{% decrement %}{% render "decr" %}',
        partials: ['decr' => '{% decrement %}'],
    );
});

test('render tag with', function () {
    assertTemplateResult(
        'Product: Draft 151cm ',
        "{% render 'product' with products[0] %}",
        assigns: [
            'products' => [['title' => 'Draft 151cm'], ['title' => 'Element 155cm']],
        ],
        partials: [
            'product' => 'Product: {{ product.title }} ',
        ],
    );
});

test('render tag with alias', function () {
    assertTemplateResult(
        'Product: Draft 151cm ',
        "{% render 'product_alias' with products[0] as product %}",
        assigns: [
            'products' => [['title' => 'Draft 151cm'], ['title' => 'Element 155cm']],
        ],
        partials: [
            'product_alias' => 'Product: {{ product.title }} ',
        ],
    );
});

test('render tag for', function () {
    assertTemplateResult(
        'Product: Draft 151cm Product: Element 155cm ',
        "{% render 'product' for products %}",
        assigns: [
            'products' => [['title' => 'Draft 151cm'], ['title' => 'Element 155cm']],
        ],
        partials: [
            'product' => 'Product: {{ product.title }} ',
        ],
    );
});

test('render tag for alias', function () {
    assertTemplateResult(
        'Product: Draft 151cm Product: Element 155cm ',
        "{% render 'product_alias' for products as product %}",
        assigns: [
            'products' => [['title' => 'Draft 151cm'], ['title' => 'Element 155cm']],
        ],
        partials: [
            'product_alias' => 'Product: {{ product.title }} ',
        ],
    );
});

test('render tag forloop', function () {
    assertTemplateResult(
        'Product: Draft 151cm first  index:1 Product: Element 155cm  last index:2 ',
        "{% render 'product' for products %}",
        assigns: [
            'products' => [['title' => 'Draft 151cm'], ['title' => 'Element 155cm']],
        ],
        partials: [
            'product' => 'Product: {{ product.title }} {% if forloop.first %}first{% endif %} {% if forloop.last %}last{% endif %} index:{{ forloop.index }} ',
        ],
    );
});

test('render tag for drop', function () {
    assertTemplateResult(
        '123',
        "{% render 'loop' for loop as value %}",
        assigns: [
            'loop' => new \Keepsuit\Liquid\Tests\Stubs\IteratorDrop(),
        ],
        partials: [
            'loop' => '{{ value.foo }}',
        ],
    );
});

test('render tag with drop', function () {
    assertTemplateResult(
        "Keepsuit\Liquid\Tests\Stubs\IteratorDrop",
        "{% render 'loop' with loop as value %}",
        assigns: [
            'loop' => new \Keepsuit\Liquid\Tests\Stubs\IteratorDrop(),
        ],
        partials: [
            'loop' => '{{ value }}',
        ],
    );
});

test('render tag renders error with template name', function () {
    assertTemplateResult(
        'Liquid error (foo line 1): Standard error',
        "{% render 'foo' with errors %}",
        assigns: [
            'errors' => new \Keepsuit\Liquid\Tests\Stubs\ErrorDrop(),
        ],
        partials: [
            'foo' => '{{ foo.standard_error }}',
        ],
        renderErrors: true
    );
});
