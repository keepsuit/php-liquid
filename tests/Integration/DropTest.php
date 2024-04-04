<?php

use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Tests\Stubs\CachableDrop;
use Keepsuit\Liquid\Tests\Stubs\CatchAllDrop;
use Keepsuit\Liquid\Tests\Stubs\ContextDrop;
use Keepsuit\Liquid\Tests\Stubs\EnumerableDrop;
use Keepsuit\Liquid\Tests\Stubs\ProductDrop;

test('product drop', function () {
    expect(renderTemplate('  ', ['product' => new ProductDrop()]))->toBe('  ');
});

test('drop does only respond to whitelisted methods', function () {
    expect(renderTemplate('{{ product.__construct }}', ['product' => new ProductDrop()]))->toBe('');
    expect(renderTemplate('{{ product.__toString }}', ['product' => new ProductDrop()]))->toBe('');
    expect(renderTemplate('{{ product.whatever }}', ['product' => new ProductDrop()]))->toBe('');
    expect(renderTemplate('{{ product | map: "__construct" }}', ['product' => new ProductDrop()]))->toBe('');
    expect(renderTemplate('{{ product | map: "__toString" }}', ['product' => new ProductDrop()]))->toBe('');
    expect(renderTemplate('{{ product | map: "whatever" }}', ['product' => new ProductDrop()]))->toBe('');
});

test('text drop', function () {
    expect(renderTemplate(' {{ product.text.text }} ', ['product' => new ProductDrop()]))->toBe(' text1 ');
});

test('catchall unknown method', function () {
    expect(renderTemplate(' {{ product.catch_all.unknown }} ', ['product' => new ProductDrop()]))->toBe(' catchall_method: unknown ');
});

test('catchall integer argument drop', function () {
    expect(renderTemplate(' {{ product.catch_all[8] }} ', ['product' => new ProductDrop()]))->toBe(' catchall_method: 8 ');
});

test('text array drop', function () {
    expect(renderTemplate('{% for text in product.text.array %} {{text}} {% endfor %}', ['product' => new ProductDrop()]))->toBe(' text1  text2 ');
});

test('context drop', function () {
    expect(renderTemplate(' {{ context.bar }} ', ['context' => new ContextDrop(), 'bar' => 'carrot']))->toBe(' carrot ');
});

test('context drop array with map', function () {
    expect(renderTemplate(' {{ contexts | map: "bar" }} ', ['contexts' => [new ContextDrop(), new ContextDrop()], 'bar' => 'carrot']))
        ->toBe(' carrotcarrot ');
});

test('nested context drop', function () {
    expect(renderTemplate(' {{ product.context.foo }} ', ['product' => new ProductDrop(), 'foo' => 'monkey']))
        ->toBe(' monkey ');
});

test('protected', function () {
    expect(renderTemplate(' {{ product.callmenot }} ', ['product' => new ProductDrop()]))
        ->toBe('  ');
});

test('php reserved methods not allowed', function () {
    foreach (['__construct', '__toString', '__get', '__set', '__call'] as $method) {
        expect(renderTemplate(sprintf(' {{ product.%s }} ', $method), ['product' => new ProductDrop()]))->toBe('  ');
    }
});

test('scope', function () {
    expect(renderTemplate('{{ context.scopes }}', ['context' => new ContextDrop()]))->toBe('1');
    expect(renderTemplate('{%for i in dummy%}{{ context.scopes }}{%endfor%}', ['context' => new ContextDrop(), 'dummy' => [1]]))->toBe('2');
    expect(renderTemplate('{%for i in dummy%}{%for i in dummy%}{{ context.scopes }}{%endfor%}{%endfor%}', ['context' => new ContextDrop(), 'dummy' => [1]]))->toBe('3');
});

test('scope through closure', function () {
    expect(renderTemplate('{{ s }}', ['context' => new ContextDrop(), 's' => fn (RenderContext $context) => $context->get('context.scopes')]))->toBe('1');
    expect(renderTemplate('{%for i in dummy%}{{ s }}{%endfor%}', ['context' => new ContextDrop(), 's' => fn (RenderContext $context) => $context->get('context.scopes'), 'dummy' => [1]]))->toBe('2');
    expect(renderTemplate('{%for i in dummy%}{%for i in dummy%}{{ s }}{%endfor%}{%endfor%}', ['context' => new ContextDrop(), 's' => fn (RenderContext $context) => $context->get('context.scopes'), 'dummy' => [1]]))->toBe('3');
});

test('scope with assign', function () {
    expect(renderTemplate('{% assign a = "variable"%}{{a}}', ['context' => new ContextDrop()]))->toBe('variable');
    expect(renderTemplate('{% assign a = "variable"%}{%for i in dummy%}{{a}}{%endfor%}', ['context' => new ContextDrop(), 'dummy' => [1]]))->toBe('variable');
    expect(renderTemplate('{% assign header_gif = "test"%}{{header_gif}}', ['context' => new ContextDrop()]))->toBe('test');
});

test('scope from tags', function () {
    expect(renderTemplate('{% for i in context.scopes_as_array %}{{i}}{% endfor %}', ['context' => new ContextDrop(), 'dummy' => [1]]))->toBe('1');
    expect(renderTemplate('{%for a in dummy%}{% for i in context.scopes_as_array %}{{i}}{% endfor %}{% endfor %}', ['context' => new ContextDrop(), 'dummy' => [1]]))->toBe('12');
    expect(renderTemplate('{%for a in dummy%}{%for a in dummy%}{% for i in context.scopes_as_array %}{{i}}{% endfor %}{% endfor %}{% endfor %}', ['context' => new ContextDrop(), 'dummy' => [1]]))->toBe('123');
});

test('access context from drop', function () {
    expect(renderTemplate('{%for a in dummy%}{{ context.loop_pos }}{% endfor %}', ['context' => new ContextDrop(), 'dummy' => [1, 2, 3]]))->toBe('123');
});

test('enumerable drop', function () {
    expect(renderTemplate('{% for c in collection %}{{c}}{% endfor %}', ['collection' => new EnumerableDrop()]))->toBe('123');
    expect(renderTemplate('{{collection.size}}', ['collection' => new EnumerableDrop()]))->toBe('3');
});

test('empty string value access', function () {
    expect(renderTemplate('{{ product[value] }}', ['product' => new ProductDrop(), 'value' => '']))->toBe('');
});

test('null value access', function () {
    expect(renderTemplate('{{ product[value] }}', ['product' => new ProductDrop(), 'value' => null]))->toBe('');
});

test('default to string on drops', function () {
    expect(renderTemplate('{{ product }}', ['product' => new ProductDrop()]))->toBe(ProductDrop::class);
    expect(renderTemplate('{{ collection }}', ['collection' => new EnumerableDrop()]))->toBe(EnumerableDrop::class);
});

test('drop metadata', function () {
    expect(invade(new ProductDrop())->getMetadata())
        ->invokableMethods->toBe(['text', 'catchAll', 'context'])
        ->cacheableMethods->toBe([])
        ->properties->toBe(['productName']);

    expect(invade(new EnumerableDrop())->getMetadata())
        ->invokableMethods->toBe(['size', 'first', 'count', 'min', 'max'])
        ->cacheableMethods->toBe([])
        ->properties->toBe([]);

    expect(invade(new CachableDrop())->getMetadata())
        ->invokableMethods->toBe(['notCached', 'cached'])
        ->cacheableMethods->toBe(['cached'])
        ->properties->toBe([]);
});

it('can cache drop method calls', function () {
    $drop = new CachableDrop();

    expect($drop)
        ->notCached->toBe(0)
        ->notCached->toBe(1);

    expect($drop)
        ->cached->toBe(0)
        ->cached->toBe(0);
});

it('can access drop data with snake and camel cases', function () {
    $drop = new ProductDrop();

    expect($drop)
        ->productName->toBe('Product')
        ->product_name->toBe('Product');

    expect($drop)
        ->catchAll->toBeInstanceOf(CatchAllDrop::class)
        ->catch_all->toBeInstanceOf(CatchAllDrop::class);
});
