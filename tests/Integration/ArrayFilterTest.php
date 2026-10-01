<?php

use Keepsuit\Liquid\Contracts\LiquidErrorHandler;
use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\Exceptions\InternalException;
use Keepsuit\Liquid\Exceptions\InvalidArgumentException;
use Keepsuit\Liquid\Exceptions\LiquidException;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Exceptions\UndefinedFilterException;
use Keepsuit\Liquid\Exceptions\UndefinedVariableException;
use Keepsuit\Liquid\Filters\FiltersProvider;
use Keepsuit\Liquid\Nodes\Range;
use Keepsuit\Liquid\Render\RenderContextOptions;
use Keepsuit\Liquid\Support\FilterSupport;
use Keepsuit\Liquid\Tests\Stubs\BooleanDrop;
use Keepsuit\Liquid\Tests\Stubs\StubFileSystem;

test('array filters normalize nested lists and scalar inputs like Shopify', function (string $source, array $data, string $expected) {
    set_error_handler(function (int $severity, string $message): never {
        throw new ErrorException($message, 0, $severity);
    });

    try {
        assertTemplateResult($expected, $source, data: $data);
    } finally {
        restore_error_handler();
    }
})->with([
    ["{{ 'x' | join }}|{{ nil | join }}|{{ true | join }}", [], 'x||true'],
    ['{{ nested | join }}', ['nested' => [[1, 2], [3, [4]]]], '1 2 3 4'],
    ['{{ nested | reverse | join }}', ['nested' => [[1, 2], [3, [4]]]], '4 3 2 1'],
    ['{{ nested | compact | size }}', ['nested' => [1, null, false, '', []]], '3'],
    ["{{ list | map: 'tags' | join: ',' }}", ['list' => [['tags' => ['x']], ['tags' => ['y']]]], 'x,y'],
]);

test('sum flattens values and reuses numeric coercion', function () {
    assertTemplateResult('10', '{{ nested | sum }}', data: ['nested' => [[1, 2], [3, [4]]]]);
    assertTemplateResult('6', "{{ items | sum: 'v' }}", data: ['items' => [['v' => [1, '2abc']], ['v' => [3, null, false]]]]);
    assertTemplateResult('3', "{{ '3abc' | sum }}");
});

test('array filter behaviour is independent of render options', function (bool $strictVariables, bool $strictFilters, bool $rethrowErrors, bool $lazyParsing, bool $stream) {
    $environment = EnvironmentFactory::new()
        ->setStrictVariables($strictVariables)
        ->setStrictFilters($strictFilters)
        ->setRethrowErrors($rethrowErrors)
        ->setLazyParsing($lazyParsing)
        ->build();
    $template = $environment->parseString("{{ nil | join }}|{{ false | join }}|{{ values | uniq | join: ',' }}|{{ values | sum }}|{{ items | where: 'v' | size }}");
    $context = $environment->newRenderContext(data: ['values' => [[1, 1.0, '1', true]], 'items' => [['v' => ''], ['v' => []], ['v' => false]]]);

    expect($stream ? implode('', iterator_to_array($template->stream($context))) : $template->render($context))
        ->toBe('|false|1,1.0,1,true|3|2')
        ->and($context->getErrors())->toBe([]);
})->with([false, true], [false, true], [false, true], [false, true], [false, true]);

test('array filters preserve strict missing variable errors including unused arguments', function (string $source, bool $rethrowErrors, bool $stream) {
    $environment = EnvironmentFactory::new()->setStrictVariables(true)->setRethrowErrors($rethrowErrors)->build();
    $context = $environment->newRenderContext(data: ['items' => []]);
    $template = $environment->parseString($source);
    $render = fn () => $stream ? implode('', iterator_to_array($template->stream($context))) : $template->render($context);

    if ($rethrowErrors) {
        expect($render)->toThrow(UndefinedVariableException::class, 'Variable `missing` not found');
    } else {
        expect($render())->toBe('');
    }

    expect($context->getErrors())->toHaveCount(1)
        ->and($context->getErrors()[0])->toBeInstanceOf(UndefinedVariableException::class);
})->with([
    '{{ missing | first }}', '{{ missing | last }}', '{{ missing | size }}', '{{ missing | join }}',
    '{{ missing | reverse }}', '{{ missing | sort }}', '{{ missing | sort_natural }}', '{{ missing | uniq }}',
    '{{ missing | compact }}', '{{ missing | sum }}', "{{ missing | map: 'v' }}", "{{ missing | where: 'v' }}",
    "{{ missing | reject: 'v' }}", "{{ missing | has: 'v' }}", "{{ missing | find: 'v' }}", "{{ missing | find_index: 'v' }}",
    '{{ missing | concat: items }}', '{{ nil | join: missing }}', '{{ nil | concat: missing }}',
    '{{ nil | map: missing }}', '{{ nil | uniq: missing }}', '{{ nil | compact: missing }}',
    '{{ nil | sum: missing }}', '{{ nil | sort: missing }}', '{{ nil | sort_natural: missing }}',
    "{{ nil | where: 'v', missing }}", "{{ nil | reject: 'v', missing }}", "{{ nil | has: 'v', missing }}",
    "{{ nil | find: 'v', missing }}", "{{ nil | find_index: 'v', missing }}",
])->with([false, true], [false, true]);

test('missing array filter input behaves like nil without strict variables', function (string $source, string $expected) {
    assertTemplateResult($expected, $source);
})->with([
    ['{{ missing | join }}', ''], ['{{ missing | first }}', ''], ['{{ missing | last }}', ''],
    ['{{ missing | size }}', '0'], ['{{ missing | sum }}', '0'], ['{{ missing | compact | size }}', '0'],
    ['{{ missing | sort | size }}', '0'], ['{{ missing | uniq | size }}', '0'],
    ["{{ missing | map: 'v' | size }}", '0'], ["{{ missing | has: 'v' }}", 'false'],
]);

test('invalid array arguments are Liquid errors with either error propagation setting', function (string $source, bool $rethrowErrors, bool $stream) {
    $environment = EnvironmentFactory::new()->setRethrowErrors($rethrowErrors)->build();
    $context = $environment->newRenderContext(data: ['values' => [1, '2'], 'empty' => []]);
    $template = $environment->parseString($source);
    $render = fn () => $stream ? implode('', iterator_to_array($template->stream($context))) : $template->render($context);

    if ($rethrowErrors) {
        expect($render)->toThrow(InvalidArgumentException::class);
    } else {
        expect($render())->toStartWith('Liquid error (line 1): ')->not->toContain('Internal exception');
    }
    expect($context->getErrors())->toHaveCount(1)
        ->and($context->getErrors()[0])->toBeInstanceOf(InvalidArgumentException::class);
})->with([
    "{{ empty | concat: 'x' }}", '{{ values | sort }}', "{{ 1 | map: 'v' }}", "{{ 1 | where: 'v' }}",
])->with([false, true], [false, true]);

test('unknown filters still follow strict filters after array coercion', function (bool $strictFilters, bool $rethrowErrors) {
    $environment = EnvironmentFactory::new()->setStrictFilters($strictFilters)->setRethrowErrors($rethrowErrors)->build();
    $context = $environment->newRenderContext();
    $template = $environment->parseString('{{ true | join | unknown }}');
    if ($strictFilters && $rethrowErrors) {
        expect(fn () => $template->render($context))->toThrow(UndefinedFilterException::class);
    } else {
        expect($template->render($context))->toBe($strictFilters ? '' : 'true');
    }
    expect($context->getErrors())->toHaveCount($strictFilters ? 1 : 0);
})->with([false, true], [false, true]);

test('array errors use the configured handler unless rethrow is enabled', function (bool $rethrowErrors, bool $stream) {
    $handler = new class implements LiquidErrorHandler
    {
        public function handle(LiquidException $exception): string
        {
            return 'handled';
        }
    };
    $environment = EnvironmentFactory::new()->setErrorHandler($handler)->setRethrowErrors($rethrowErrors)->build();
    $template = $environment->parseString("{{ nil | concat: 'x' }}");
    $context = $environment->newRenderContext();
    $render = fn () => $stream ? implode('', iterator_to_array($template->stream($context))) : $template->render($context);
    if ($rethrowErrors) {
        expect($render)->toThrow(InvalidArgumentException::class);
    } else {
        expect($render())->toBe('handled');
    }
    expect($context->getErrors())->not->toBeEmpty();
})->with([false, true], [false, true]);

test('array coercion and context overrides work in partials with either lazy parsing setting', function (bool $lazyParsing, bool $stream) {
    $environment = EnvironmentFactory::new()->setFilesystem(new StubFileSystem(['array' => "{{ value | join: ',' }}|{{ nil | join }}"]))->build();
    $template = $environment->parseString("{% render 'array', value: values %}");
    $context = $environment->newRenderContext(
        data: ['values' => [[1, false], ['x']]],
        options: new RenderContextOptions(strictVariables: true, strictFilters: true, rethrowErrors: true, lazyParsing: $lazyParsing),
    );
    expect($stream ? implode('', iterator_to_array($template->stream($context))) : $template->render($context))
        ->toBe('1,false,x|')->and($context->getErrors())->toBe([]);
})->with([false, true], [false, true]);

test('array filters normalize Liquid values and iterator keys', function () {
    $context = EnvironmentFactory::new()->build()->newRenderContext();
    expect($context->applyFilter('join', new ArrayIterator([4 => 'a', 9 => 'b']), [',']))->toBe('a,b');
    expect($context->applyFilter('sort', new ArrayIterator(['first' => 2, 'second' => 1])))->toBe([1, 2]);
    expect($context->applyFilter('find_index', new ArrayIterator([4 => ['v' => false], 9 => ['v' => true]]), ['v']))->toBe(1);
    expect($context->applyFilter('reverse', new Range(1, 3)))->toBe([3, 2, 1]);
    expect($context->applyFilter('where', [['v' => new BooleanDrop(false)], ['v' => new BooleanDrop(true)]], ['v']))->toHaveCount(1);
    expect($context->applyFilter('map', [['v' => fn () => 'value']], ['v']))->toBe(['value']);
});

test('array filter syntax remains strict regardless of render options', function () {
    $environment = EnvironmentFactory::new()->setRethrowErrors(false)->setStrictVariables(false)->setStrictFilters(false)->build();
    expect(fn () => $environment->parseString("{{ nil | join: 'x' trailing }}"))->toThrow(SyntaxException::class);
});

test('custom array filters retain their parameter types', function () {
    $factory = EnvironmentFactory::new()->registerFilters(ArrayFilterOverride::class);
    expect(renderTemplate("{{ values | join: ',' }}", data: ['values' => [1, 2]], factory: $factory))->toBe('custom');
    expect(fn () => renderTemplate('{{ nil | join }}', factory: $factory))->toThrow(InternalException::class);
});

class ArrayFilterOverride extends FiltersProvider
{
    public function join(array $input, string $glue = ' '): string
    {
        return 'custom';
    }
}

test('custom filters can compose internal filter support with the current context', function () {
    $environment = EnvironmentFactory::new()->registerFilters(ArraySupportFilters::class)->build();
    $template = $environment->parseString('{{ items | labels }}');
    $drop = new \Keepsuit\Liquid\Tests\Stubs\ContextDrop;
    foreach (['first', 'second'] as $label) {
        $context = $environment->newRenderContext(data: ['items' => [[['label' => fn () => 1.0]], $drop], 'label' => $label]);
        expect($template->render($context))->toBe('1.0|'.$label);
    }
    expect($environment->filterRegistry->has('set_context'))->toBeFalse();
});

test('standard filter support follows context changes and updates in the same context', function () {
    $environment = EnvironmentFactory::new()->build();
    $first = $environment->newRenderContext(data: ['label' => 'first']);
    $second = $environment->newRenderContext(data: ['label' => 'second']);
    $drop = new \Keepsuit\Liquid\Tests\Stubs\ContextDrop;

    expect($first->applyFilter('map', [$drop], ['label']))->toBe(['first']);
    expect($first->applyFilter('map', [$drop], ['label']))->toBe(['first']);
    expect($second->applyFilter('map', [$drop], ['label']))->toBe(['second']);

    $first->set('label', 'updated');
    expect($first->applyFilter('map', [$drop], ['label']))->toBe(['updated']);

    $first->set('label', 'again');
    expect($first->applyFilter('map', [$drop], ['label']))->toBe(['again']);
});

class ArraySupportFilters extends FiltersProvider
{
    public function labels(mixed $input): string
    {
        $support = new FilterSupport($this->context);
        $labels = [];
        foreach ($support->iterate($input) as $item) {
            $labels[] = $support->stringify($support->property($item, 'label'));
        }

        return implode('|', $labels);
    }
}

test('map preserves property arrays and treats a hash as one item', function () {
    $context = EnvironmentFactory::new()->build()->newRenderContext();
    expect($context->applyFilter('map', [['tags' => ['x', 'y']]], ['tags']))->toBe([['x', 'y']]);
    expect($context->applyFilter('map', ['a' => 1], ['a']))->toBe([1]);
    expect($context->applyFilter('map', ['a' => 1], ['missing']))->toBe([null]);
    expect($context->applyFilter('map', null, ['a']))->toBe([]);
    expect($context->applyFilter('map', 'abcdef', ['cd']))->toBe(['cd']);
});

test('sort filters flatten lists and report incompatible types as Liquid errors', function () {
    $context = EnvironmentFactory::new()->build()->newRenderContext();
    expect($context->applyFilter('sort', [[3, 1], [2, null]]))->toBe([1, 2, 3, null]);
    expect($context->applyFilter('sort_natural', [['b'], ['A', 'c']]))->toBe(['A', 'b', 'c']);
    expect($context->applyFilter('sort', null))->toBe([]);
    expect($context->applyFilter('sort_natural', [true, false]))->toBe([false, true]);
    expect(fn () => $context->applyFilter('sort', [1, '2']))->toThrow(InvalidArgumentException::class);
    expect($context->applyFilter('sort', [['a' => 1], ['a' => 1.0]]))->toBe([['a' => 1], ['a' => 1.0]]);
});

test('first last and size preserve shape and safely accept scalars', function () {
    $context = EnvironmentFactory::new()->build()->newRenderContext();
    foreach ([null, false, true, 1.0] as $value) {
        expect($context->applyFilter('first', $value))->toBeNull();
        expect($context->applyFilter('last', $value))->toBeNull();
        expect($context->applyFilter('size', $value))->toBe(0);
    }
    expect($context->applyFilter('first', 1))->toBeNull();
    expect($context->applyFilter('last', 1))->toBeNull();
    expect($context->applyFilter('size', 1))->toBe(8);
    expect($context->applyFilter('first', [[1, 2], [3]]))->toBe([1, 2]);
    expect($context->applyFilter('last', [[1, 2], [3]]))->toBe([3]);
    expect($context->applyFilter('first', ['a' => 1, 'b' => 2]))->toBe(['a', 1]);
    expect($context->applyFilter('last', ['a' => 1, 'b' => 2]))->toBeNull();
    assertTemplateResult('H|o', "{{ 'Hello' | first }}|{{ 'Hello' | last }}");
});

test('singleton uniq and sort do not read unused properties', function () {
    $context = EnvironmentFactory::new()->build()->newRenderContext();
    foreach (['uniq', 'sort', 'sort_natural'] as $filter) {
        expect($context->applyFilter($filter, 1, ['v']))->toBe([1]);
    }
    expect($context->applyFilter('sort', [1, false], ['v']))->toBeNull();
    expect($context->applyFilter('uniq', [false, true], ['v']))->toBeNull();
});

test('uniq preserves types and compares hash content and drop identity', function () {
    $context = EnvironmentFactory::new()->build()->newRenderContext();
    $first = new \Keepsuit\Liquid\Tests\Stubs\TestDrop('a');
    $second = new \Keepsuit\Liquid\Tests\Stubs\TestDrop('a');
    expect($context->applyFilter('uniq', [1, '1', 1.0, true, 'a', 'a']))->toBe([1, '1', 1.0, true, 'a']);
    expect($context->applyFilter('uniq', [['a' => 1, 'b' => 2], ['b' => 2, 'a' => 1], ['a' => 2]]))
        ->toBe([['a' => 1, 'b' => 2], ['a' => 2]]);
    expect($context->applyFilter('uniq', [$first, $second, $first]))->toBe([$first, $second]);
    assertTemplateResult('1,1,1.0,true,a', "{{ x | uniq | join: ',' }}", data: ['x' => [1, '1', 1.0, true, 'a', 'a']]);
});

test('array selectors use Liquid truthiness and condition equality', function () {
    $context = EnvironmentFactory::new()->build()->newRenderContext();
    $items = [['v' => false], ['v' => ''], ['v' => []], ['v' => 0], ['v' => 2], ['v' => null]];
    expect($context->applyFilter('where', $items, ['v']))->toBe([['v' => ''], ['v' => []], ['v' => 0], ['v' => 2]]);
    expect($context->applyFilter('reject', $items, ['v']))->toBe([['v' => false], ['v' => null]]);
    expect($context->applyFilter('has', [['v' => '']], ['v']))->toBeTrue();
    expect($context->applyFilter('find', $items, ['v']))->toBe(['v' => '']);
    expect($context->applyFilter('find_index', $items, ['v']))->toBe(1);
    $numbers = [['a' => 1], ['a' => '1'], ['a' => 1.0]];
    expect($context->applyFilter('where', $numbers, ['a', 1]))->toBe([['a' => 1], ['a' => 1.0]]);
    expect($context->applyFilter('reject', $numbers, ['a', 1]))->toBe([['a' => '1']]);
    expect($context->applyFilter('find_index', [['a' => '1'], ['a' => 1.0]], ['a', 1]))->toBe(1);
});

test('concat flattens its input and retains the appended array structure', function () {
    $environment = EnvironmentFactory::new()->build();
    $context = $environment->newRenderContext();
    expect($context->applyFilter('concat', [[1, 2]], [[[3, 4]]]))->toBe([1, 2, [3, 4]]);
    expect($context->applyFilter('concat', null, [[3]]))->toBe([3]);
    expect($context->applyFilter('concat', [], [[fn () => 'value']]))->toBe(['value']);
    $drop = new \Keepsuit\Liquid\Tests\Stubs\ContextDrop;
    expect($context->applyFilter('concat', [], [[$drop]]))->toBe([$drop]);
    $context->set('label', 'bound');
    expect($drop->label)->toBe('bound');
    expect($context->applyFilter('concat', [], [[new \Keepsuit\Liquid\Tests\Stubs\NumberDrop(5)]]))->toBe([5]);
    expect(fn () => $context->applyFilter('concat', [], ['invalid']))->toThrow(InvalidArgumentException::class);
});

test('join stringifies hashes and preserves float and boolean types', function () {
    assertTemplateResult('{"a"=>1, "b"=>[1.0, false, nil, "é"]}', '{{ h | join }}', data: ['h' => ['a' => 1, 'b' => [1.0, false, null, 'é']]]);
    assertTemplateResult('a1.0b', '{{ items | join: 1.0 }}', data: ['items' => ['a', 'b']]);
});

test('partial errors inherit context overrides', function (bool $lazyParsing, bool $stream) {
    $environment = EnvironmentFactory::new()->setFilesystem(new StubFileSystem(['array' => '{{ missing | join }}']))->build();
    $template = $environment->parseString("{% render 'array' %}");
    $context = $environment->newRenderContext(options: new RenderContextOptions(strictVariables: true, rethrowErrors: true, lazyParsing: $lazyParsing));
    expect(fn () => $stream ? implode('', iterator_to_array($template->stream($context))) : $template->render($context))
        ->toThrow(UndefinedVariableException::class);
    expect($context->getErrors())->not->toBeEmpty();
})->with([false, true], [false, true]);

test('uniq treats signed zero as equal and preserves strict nested hash values', function () {
    $context = EnvironmentFactory::new()->build()->newRenderContext();
    expect($context->applyFilter('uniq', [0.0, -0.0]))->toBe([0.0]);
    expect($context->applyFilter('uniq', [['a' => [1]], ['a' => [1.0]], ['a' => [1]]]))
        ->toBe([['a' => [1]], ['a' => [1.0]]]);
});
