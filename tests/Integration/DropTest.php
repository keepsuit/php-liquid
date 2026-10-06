<?php

use Keepsuit\Liquid\Environment;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Tests\Stubs\CachableDrop;
use Keepsuit\Liquid\Tests\Stubs\CatchAllDrop;
use Keepsuit\Liquid\Tests\Stubs\ContextDrop;
use Keepsuit\Liquid\Tests\Stubs\EnumerableDrop;
use Keepsuit\Liquid\Tests\Stubs\ProductDrop;

describe('rendering with template backends', function () {
    test('product drop', function (bool $compiled) {
        expect(renderTemplate('  ', ['product' => new ProductDrop], compiled: $compiled))->toBe('  ');
    });

    test('drop does only respond to whitelisted methods', function (bool $compiled) {
        expect(renderTemplate('{{ product.__construct }}', ['product' => new ProductDrop], compiled: $compiled))->toBe('');
        expect(renderTemplate('{{ product.__toString }}', ['product' => new ProductDrop], compiled: $compiled))->toBe('');
        expect(renderTemplate('{{ product.whatever }}', ['product' => new ProductDrop], compiled: $compiled))->toBe('');
        expect(renderTemplate('{{ product | map: "__construct" }}', ['product' => new ProductDrop], compiled: $compiled))->toBe('');
        expect(renderTemplate('{{ product | map: "__toString" }}', ['product' => new ProductDrop], compiled: $compiled))->toBe('');
        expect(renderTemplate('{{ product | map: "whatever" }}', ['product' => new ProductDrop], compiled: $compiled))->toBe('');
    });

    test('text drop', function (bool $compiled) {
        expect(renderTemplate(' {{ product.text.text }} ', ['product' => new ProductDrop], compiled: $compiled))->toBe(' text1 ');
    });

    test('catchall unknown method', function (bool $compiled) {
        expect(renderTemplate(' {{ product.catch_all.unknown }} ', ['product' => new ProductDrop], compiled: $compiled))->toBe(' catchall_method: unknown ');
    });

    test('catchall integer argument drop', function (bool $compiled) {
        expect(renderTemplate(' {{ product.catch_all[8] }} ', ['product' => new ProductDrop], compiled: $compiled))->toBe(' catchall_method: 8 ');
    });

    test('text array drop', function (bool $compiled) {
        expect(renderTemplate('{% for text in product.text.array %} {{text}} {% endfor %}', ['product' => new ProductDrop], compiled: $compiled))->toBe(' text1  text2 ');
    });

    test('context drop', function (bool $compiled) {
        expect(renderTemplate(' {{ context.bar }} ', ['context' => new ContextDrop, 'bar' => 'carrot'], compiled: $compiled))->toBe(' carrot ');
    });

    test('context drop array with map', function (bool $compiled) {
        expect(renderTemplate(' {{ contexts | map: "bar" }} ', ['contexts' => [new ContextDrop, new ContextDrop], 'bar' => 'carrot'], compiled: $compiled))
            ->toBe(' carrotcarrot ');
    });

    test('nested context drop', function (bool $compiled) {
        expect(renderTemplate(' {{ product.context.foo }} ', ['product' => new ProductDrop, 'foo' => 'monkey'], compiled: $compiled))
            ->toBe(' monkey ');
    });

    test('protected', function (bool $compiled) {
        expect(renderTemplate(' {{ product.callmenot }} ', ['product' => new ProductDrop], compiled: $compiled))
            ->toBe('  ');
    });

    test('php reserved methods not allowed', function (bool $compiled) {
        foreach (['__construct', '__toString', '__get', '__set', '__call'] as $method) {
            expect(renderTemplate(sprintf(' {{ product.%s }} ', $method), ['product' => new ProductDrop], compiled: $compiled))->toBe('  ');
        }
    });

    test('scope', function (bool $compiled) {
        expect(renderTemplate('{{ context.scopes }}', ['context' => new ContextDrop], compiled: $compiled))->toBe('1');
        expect(renderTemplate('{%for i in dummy%}{{ context.scopes }}{%endfor%}', ['context' => new ContextDrop, 'dummy' => [1]], compiled: $compiled))->toBe('2');
        expect(renderTemplate('{%for i in dummy%}{%for i in dummy%}{{ context.scopes }}{%endfor%}{%endfor%}', ['context' => new ContextDrop, 'dummy' => [1]], compiled: $compiled))->toBe('3');
    });

    test('scope through closure', function (bool $compiled) {
        expect(renderTemplate('{{ s }}', ['context' => new ContextDrop, 's' => fn (RenderContext $context) => $context->get('context.scopes')], compiled: $compiled))->toBe('1');
        expect(renderTemplate('{%for i in dummy%}{{ s }}{%endfor%}', ['context' => new ContextDrop, 's' => fn (RenderContext $context) => $context->get('context.scopes'), 'dummy' => [1]], compiled: $compiled))->toBe('2');
        expect(renderTemplate('{%for i in dummy%}{%for i in dummy%}{{ s }}{%endfor%}{%endfor%}', ['context' => new ContextDrop, 's' => fn (RenderContext $context) => $context->get('context.scopes'), 'dummy' => [1]], compiled: $compiled))->toBe('3');
    });

    test('scope with assign', function (bool $compiled) {
        expect(renderTemplate('{% assign a = "variable"%}{{a}}', ['context' => new ContextDrop], compiled: $compiled))->toBe('variable');
        expect(renderTemplate('{% assign a = "variable"%}{%for i in dummy%}{{a}}{%endfor%}', ['context' => new ContextDrop, 'dummy' => [1]], compiled: $compiled))->toBe('variable');
        expect(renderTemplate('{% assign header_gif = "test"%}{{header_gif}}', ['context' => new ContextDrop], compiled: $compiled))->toBe('test');
    });

    test('scope from tags', function (bool $compiled) {
        expect(renderTemplate('{% for i in context.scopes_as_array %}{{i}}{% endfor %}', ['context' => new ContextDrop, 'dummy' => [1]], compiled: $compiled))->toBe('1');
        expect(renderTemplate('{%for a in dummy%}{% for i in context.scopes_as_array %}{{i}}{% endfor %}{% endfor %}', ['context' => new ContextDrop, 'dummy' => [1]], compiled: $compiled))->toBe('12');
        expect(renderTemplate('{%for a in dummy%}{%for a in dummy%}{% for i in context.scopes_as_array %}{{i}}{% endfor %}{% endfor %}{% endfor %}', ['context' => new ContextDrop, 'dummy' => [1]], compiled: $compiled))->toBe('123');
    });

    test('access context from drop', function (bool $compiled) {
        expect(renderTemplate('{%for a in dummy%}{{ context.loop_pos }}{% endfor %}', ['context' => new ContextDrop, 'dummy' => [1, 2, 3]], compiled: $compiled))->toBe('123');
    });

    test('enumerable drop', function (bool $compiled) {
        expect(renderTemplate('{% for c in collection %}{{c}}{% endfor %}', ['collection' => new EnumerableDrop], compiled: $compiled))->toBe('123');
        expect(renderTemplate('{{collection.size}}', ['collection' => new EnumerableDrop], compiled: $compiled))->toBe('3');
    });

    test('empty string value access', function (bool $compiled) {
        expect(renderTemplate('{{ product[value] }}', ['product' => new ProductDrop, 'value' => ''], compiled: $compiled))->toBe('');
    });

    test('null value access', function (bool $compiled) {
        expect(renderTemplate('{{ product[value] }}', ['product' => new ProductDrop, 'value' => null], compiled: $compiled))->toBe('');
    });

    test('default to string on drops', function (bool $compiled) {
        expect(renderTemplate('{{ product }}', ['product' => new ProductDrop], compiled: $compiled))->toBe(ProductDrop::class);
        expect(renderTemplate('{{ collection }}', ['collection' => new EnumerableDrop], compiled: $compiled))->toBe(EnumerableDrop::class);
    });
})->with('template backends');

test('drop toArray', function () {
    $context = Environment::default()->newRenderContext();

    $drop = new ProductDrop;
    $drop->setContext($context);
    expect($drop->toArray())
        ->toHaveKeys(['product_name', 'text', 'catch_all', 'context'])
        ->product_name->toBe('Product')
        ->text->toBeInstanceOf(\Keepsuit\Liquid\Tests\Stubs\TextDrop::class)
        ->catch_all->toBeInstanceOf(\Keepsuit\Liquid\Tests\Stubs\CatchAllDrop::class)
        ->context->toBe($context);
});

test('drop toArray dynamic properties', function () {
    $context = Environment::default()->newRenderContext();

    $drop = new \Keepsuit\Liquid\Drops\ForLoopDrop('for', 10);
    $drop->setContext($context);

    expect($drop->toArray())
        ->toHaveKeys(['length', 'index', 'index0', 'rindex', 'rindex0', 'first', 'last', 'name'])
        ->not->toHaveKey('context');
});

test('drop metadata', function () {
    expect(invade(new ProductDrop)->getMetadata())
        ->invokableMethods->toBe(['text', 'catchAll', 'context'])
        ->cacheableMethods->toBe([])
        ->properties->toBe(['productName']);

    expect(invade(new EnumerableDrop)->getMetadata())
        ->invokableMethods->toBe(['size', 'first', 'count', 'min', 'max'])
        ->cacheableMethods->toBe([])
        ->properties->toBe([]);

    expect(invade(new CachableDrop)->getMetadata())
        ->invokableMethods->toBe(['notCached', 'cached', 'cachedNull', 'cachedNullCalls'])
        ->cacheableMethods->toBe(['cached', 'cachedNull'])
        ->properties->toBe([]);
});

it('can cache drop method calls', function () {
    $drop = new CachableDrop;
    $anotherDrop = new CachableDrop;

    expect($drop)
        ->notCached->toBe(0)
        ->notCached->toBe(1);

    expect($drop)
        ->cached->toBe(0)
        ->cached->toBe(0)
        ->and($anotherDrop)
        ->cached->toBe(0);
});

it('can cache null drop method results', function () {
    $drop = new CachableDrop;

    expect($drop)
        ->cachedNull->toBeNull()
        ->cachedNull->toBeNull()
        ->cachedNullCalls->toBe(1);
});

it('does not cache dynamic drop method results', function () {
    $drop = new class extends \Keepsuit\Liquid\Drop
    {
        private int $calls = 0;

        protected function liquidMethodMissing(string $name): mixed
        {
            return sprintf('%s:%d', $name, ++$this->calls);
        }
    };

    expect($drop)
        ->unknownValue->toBe('unknownValue:1')
        ->unknownValue->toBe('unknownValue:2');
});

it('can access drop data with snake and camel cases', function () {
    $drop = new ProductDrop;

    expect($drop)
        ->productName->toBe('Product')
        ->product_name->toBe('Product');

    expect($drop)
        ->catchAll->toBeInstanceOf(CatchAllDrop::class)
        ->catch_all->toBeInstanceOf(CatchAllDrop::class);
});
