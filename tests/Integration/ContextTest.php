<?php

use Keepsuit\Liquid\Nodes\Range;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Render\RenderContextOptions;
use Keepsuit\Liquid\Support\UndefinedVariable;
use Keepsuit\Liquid\Tests\Stubs\Category;
use Keepsuit\Liquid\Tests\Stubs\CategoryDrop;
use Keepsuit\Liquid\Tests\Stubs\CentsDrop;
use Keepsuit\Liquid\Tests\Stubs\ContextSensitiveDrop;
use Keepsuit\Liquid\Tests\Stubs\CounterDrop;
use Keepsuit\Liquid\Tests\Stubs\HundredCents;

test('variables', function (bool $strict) {
    $context = new RenderContext(options: new RenderContextOptions(strictVariables: $strict));

    $context->set('string', 'string');
    expect($context->get('string'))->toBe('string');

    $context->set('num', 5);
    expect($context->get('num'))->toBe(5);

    $context->set('bool', true);
    expect($context->get('bool'))->toBe(true);
    $context->set('bool', false);
    expect($context->get('bool'))->toBe(false);

    $context->set('date', new DateTime('2019-01-01'));
    expect($context->get('date'))->toBeInstanceOf(DateTime::class);

    $context->set('nil', null);
    expect($context->get('nil'))->toBe(null);
})->with([
    'default' => false,
    'strict' => true,
]);

test('variables not existing', function (bool $strict) {
    $context = new RenderContext(options: new RenderContextOptions(strictVariables: $strict));

    if ($strict) {
        expect($context->get('does_not_exist'))->toBeInstanceOf(UndefinedVariable::class);
        expect(fn () => renderTemplate('{{ does_not_exists }}', strictVariables: true))->toThrow(\Keepsuit\Liquid\Exceptions\UndefinedVariableException::class, 'Variable `does_not_exists` not found');
    } else {
        expect($context->get('does_not_exist'))->toBeNull();
        assertTemplateResult('', '{{ does_not_exists }}', strictVariables: $strict);
    }

    assertTemplateResult('true', '{% if does_not_exist == nil %}true{% endif %}', strictVariables: $strict);
})->with([
    'default' => false,
    'strict' => true,
]);

test('array size', function (bool $strict) {
    assertTemplateResult(
        'true',
        '{% if numbers.size == 4 %}true{% endif %}',
        ['numbers' => [1, 2, 3, 4]],
        strictVariables: $strict
    );
    assertTemplateResult(
        'true',
        '{% if numbers.size == 4 %}true{% endif %}',
        ['numbers' => [1 => 1, 2 => 2, 3 => 3, 4 => 4]],
        strictVariables: $strict
    );
    assertTemplateResult(
        'true',
        '{% if numbers.size == 1000 %}true{% endif %}',
        ['numbers' => [1 => 1, 2 => 2, 3 => 3, 4 => 4, 'size' => 1000]],
        strictVariables: $strict
    );
})->with([
    'default' => false,
    'strict' => true,
]);

test('hyphenated variable', function (bool $strict) {
    assertTemplateResult('godz', '{{ oh-my }}', ['oh-my' => 'godz'], strictVariables: $strict);
})->with([
    'default' => false,
    'strict' => true,
]);

test('add filter', function (bool $strict) {
    $context = \Keepsuit\Liquid\EnvironmentFactory::new()
        ->registerFilters(\Keepsuit\Liquid\Tests\Stubs\TestFilters::class)
        ->build()
        ->newRenderContext();

    expect($context->applyFilter('hi', 'hi?'))->toBe('hi? hi!');

    $context = \Keepsuit\Liquid\EnvironmentFactory::new()
        ->build()
        ->newRenderContext();
    expect($context->applyFilter('hi', 'hi?'))->toBe('hi?');
})->with([
    'default' => false,
    'strict' => true,
]);

test('add item in outer scope', function (bool $strict) {
    $context = new RenderContext(options: new RenderContextOptions(strictVariables: $strict));
    $context->set('test', 'test');
    $context->stack(function () use ($context) {
        expect($context->get('test'))->toBe('test');
    });
    expect($context->get('test'))->toBe('test');
})->with([
    'default' => false,
    'strict' => true,
]);

test('add item in inner scope', function (bool $strict) {
    $context = new RenderContext(options: new RenderContextOptions(strictVariables: $strict));
    $context->stack(function () use ($context) {
        $context->set('test', 'test');

        expect($context->get('test'))->toBe('test');
    });

    if ($strict) {
        expect($context->get('test'))->toBeInstanceOf(UndefinedVariable::class);
    } else {
        expect($context->get('test'))->toBeNull();
    }
})->with([
    'default' => false,
    'strict' => true,
]);

test('hierarchical data', function (bool $strict) {
    $assigns = ['hash' => ['name' => 'tobi']];
    assertTemplateResult('tobi', '{{ hash.name }}', $assigns, strictVariables: $strict);
    assertTemplateResult('tobi', '{{ hash["name"] }}', $assigns, strictVariables: $strict);
})->with([
    'default' => false,
    'strict' => true,
]);

test('keywords', function (bool $strict) {
    assertTemplateResult('pass', '{% if true == expect %}pass{% endif %}', ['expect' => true], strictVariables: $strict);
    assertTemplateResult('pass', '{% if false == expect %}pass{% endif %}', ['expect' => false], strictVariables: $strict);
})->with([
    'default' => false,
    'strict' => true,
]);

test('digits', function (bool $strict) {
    assertTemplateResult('pass', '{% if 100 == expect %}pass{% endif %}', ['expect' => 100], strictVariables: $strict);
    assertTemplateResult('pass', '{% if 100.00 == expect %}pass{% endif %}', ['expect' => 100.00], strictVariables: $strict);
})->with([
    'default' => false,
    'strict' => true,
]);

test('strings', function (bool $strict) {
    assertTemplateResult('hello!', '{{ "hello!" }}', strictVariables: $strict);
    assertTemplateResult('hello!', "{{ 'hello!' }}", strictVariables: $strict);
})->with([
    'default' => false,
    'strict' => true,
]);

test('merge', function (bool $strict) {
    $context = new RenderContext(options: new RenderContextOptions(strictVariables: $strict));
    $context->merge(['test' => 'test']);
    expect($context->get('test'))->toBe('test');

    $context->merge(['test' => 'newvalue', 'foo' => 'bar']);
    expect($context)
        ->get('test')->toBe('newvalue')
        ->get('foo')->toBe('bar');
})->with([
    'default' => false,
    'strict' => true,
]);

test('array notation', function (bool $strict) {
    $assigns = ['test' => ['a', 'b']];
    assertTemplateResult('a', '{{ test[0] }}', $assigns, strictVariables: $strict);
    assertTemplateResult('b', '{{ test[1] }}', $assigns, strictVariables: $strict);
    assertTemplateResult('pass', '{% if test[2] == nil %}pass{% endif %}', $assigns, strictVariables: $strict);
})->with([
    'default' => false,
    'strict' => true,
]);

test('recursive array notation', function (bool $strict) {
    $assigns = ['test' => ['test' => [1, 2, 3, 4, 5]]];
    assertTemplateResult('1', '{{ test.test[0] }}', $assigns, strictVariables: $strict);

    $assigns = ['test' => [['test' => 'worked']]];
    assertTemplateResult('worked', '{{ test[0].test }}', $assigns, strictVariables: $strict);
})->with([
    'default' => false,
    'strict' => true,
]);

test('hash to array transition', function (bool $strict) {
    $assigns = [
        'colors' => [
            'Blue' => ['003366', '336699', '6699CC', '99CCFF'],
            'Green' => ['003300', '336633', '669966', '99CC99'],
            'Yellow' => ['CC9900', 'FFCC00', 'FFFF99', 'FFFFCC'],
            'Red' => ['660000', '993333', 'CC6666', 'FF9999'],
        ],
    ];

    assertTemplateResult('003366', '{{ colors.Blue[0] }}', $assigns, strictVariables: $strict);
    assertTemplateResult('FF9999', '{{ colors.Red[3] }}', $assigns, strictVariables: $strict);
})->with([
    'default' => false,
    'strict' => true,
]);

test('array first/last', function (bool $strict) {
    $assigns = ['test' => [1, 2, 3, 4, 5]];
    assertTemplateResult('1', '{{ test.first }}', $assigns, strictVariables: $strict);
    assertTemplateResult('pass', '{% if test.last == 5 %}pass{% endif %}', $assigns, strictVariables: $strict);

    $assigns = ['test' => ['test' => [1, 2, 3, 4, 5]]];
    assertTemplateResult('1', '{{ test.test.first }}', $assigns, strictVariables: $strict);
    assertTemplateResult('5', '{{ test.test.last }}', $assigns, strictVariables: $strict);

    $assigns = ['test' => [1]];
    assertTemplateResult('1', '{{ test.first }}', $assigns, strictVariables: $strict);
    assertTemplateResult('1', '{{ test.last }}', $assigns, strictVariables: $strict);
})->with([
    'default' => false,
    'strict' => true,
]);

test('access hashes with hash notation', function (bool $strict) {
    $assigns = ['products' => ['count' => 5, 'tags' => ['deepsnow', 'freestyle']]];
    assertTemplateResult('5', '{{ products["count"] }}', $assigns, strictVariables: $strict);
    assertTemplateResult('deepsnow', '{{ products["tags"][0] }}', $assigns, strictVariables: $strict);
    assertTemplateResult('deepsnow', '{{ products["tags"].first }}', $assigns, strictVariables: $strict);

    $assigns = ['product' => ['variants' => [['title' => 'draft151cm'], ['title' => 'element151cm']]]];
    assertTemplateResult('draft151cm', '{{ product["variants"][0]["title"] }}', $assigns, strictVariables: $strict);
    assertTemplateResult('element151cm', '{{ product["variants"][1]["title"] }}', $assigns, strictVariables: $strict);
    assertTemplateResult('draft151cm', '{{ product["variants"].first["title"] }}', $assigns, strictVariables: $strict);
    assertTemplateResult('element151cm', '{{ product["variants"].last["title"] }}', $assigns, strictVariables: $strict);
})->with([
    'default' => false,
    'strict' => true,
]);

test('access hashes with hash access variables', function (bool $strict) {
    $assigns = [
        'var' => 'tags',
        'nested' => ['var' => 'tags'],
        'products' => ['count' => 5, 'tags' => ['deepsnow', 'freestyle']],
    ];

    assertTemplateResult('deepsnow', '{{ products[var].first }}', $assigns, strictVariables: $strict);
    assertTemplateResult('freestyle', '{{ products[nested.var].last }}', $assigns, strictVariables: $strict);
})->with([
    'default' => false,
    'strict' => true,
]);

test('hash notation for lookup filters', function (bool $strict) {
    assertTemplateResult('1', '{{ value.first }}', ['value' => [1, 2, 3, 4, 5]], strictVariables: $strict);
    assertTemplateResult('1', '{{ value["first"] }}', ['value' => [1, 2, 3, 4, 5]], strictVariables: $strict);

    assertTemplateResult('Hello', '{{ value["first"] }}', ['value' => ['first' => 'Hello']], strictVariables: $strict);
    assertTemplateResult('', '{{ value["first"] }}', ['value' => ['key' => 'value']], strictVariables: $strict);
})->with([
    'default' => false,
    'strict' => true,
]);

test('first can appear in middle of call chain', function (bool $strict) {
    $assigns = ['product' => ['variants' => [['title' => 'draft151cm'], ['title' => 'element151cm']]]];

    assertTemplateResult('draft151cm', '{{ product.variants[0].title }}', $assigns, strictVariables: $strict);
    assertTemplateResult('element151cm', '{{ product.variants[1].title }}', $assigns, strictVariables: $strict);
    assertTemplateResult('draft151cm', '{{ product.variants.first.title }}', $assigns, strictVariables: $strict);
    assertTemplateResult('element151cm', '{{ product.variants.last.title }}', $assigns, strictVariables: $strict);
})->with([
    'default' => false,
    'strict' => true,
]);

test('cents', function (bool $strict) {
    $context = new RenderContext(options: new RenderContextOptions(strictVariables: $strict));
    $context->merge(['cents' => new HundredCents]);
    expect($context->get('cents'))->toBe(100);
})->with([
    'default' => false,
    'strict' => true,
]);

test('nested cents', function (bool $strict) {
    $context = new RenderContext(options: new RenderContextOptions(strictVariables: $strict));
    $context->merge(['cents' => ['amount' => new HundredCents]]);
    expect($context->get('cents.amount'))->toBe(100);

    $context->merge(['cents' => ['cents' => ['amount' => new HundredCents]]]);
    expect($context->get('cents.cents.amount'))->toBe(100);
})->with([
    'default' => false,
    'strict' => true,
]);

test('cents through drop', function (bool $strict) {
    $context = new RenderContext(options: new RenderContextOptions(strictVariables: $strict));
    $context->merge(['cents' => new CentsDrop]);
    expect($context->get('cents.amount'))->toBe(100);
})->with([
    'default' => false,
    'strict' => true,
]);

test('nested cents through drop', function (bool $strict) {
    $context = new RenderContext(options: new RenderContextOptions(strictVariables: $strict));
    $context->merge(['vars' => ['cents' => new CentsDrop]]);
    expect($context->get('vars.cents.amount'))->toBe(100);
})->with([
    'default' => false,
    'strict' => true,
]);

test('cents through drop nestedly', function (bool $strict) {
    $context = new RenderContext(options: new RenderContextOptions(strictVariables: $strict));
    $context->merge(['cents' => ['cents' => new CentsDrop]]);
    expect($context->get('cents.cents.amount'))->toBe(100);

    $context->merge(['cents' => ['cents' => ['cents' => new CentsDrop]]]);
    expect($context->get('cents.cents.cents.amount'))->toBe(100);
})->with([
    'default' => false,
    'strict' => true,
]);

test('context from within drop', function (bool $strict) {
    $context = new RenderContext(options: new RenderContextOptions(strictVariables: $strict));
    $context->merge(['test' => '123', 'vars' => new ContextSensitiveDrop]);
    expect($context->get('vars.test'))->toBe('123');
})->with([
    'default' => false,
    'strict' => true,
]);

test('nested context from within drop', function (bool $strict) {
    $context = new RenderContext(options: new RenderContextOptions(strictVariables: $strict));
    $context->merge(['test' => '123', 'vars' => ['local' => new ContextSensitiveDrop]]);
    expect($context->get('vars.local.test'))->toBe('123');
})->with([
    'default' => false,
    'strict' => true,
]);

test('ranges', function (bool $strict) {
    assertTemplateResult('1..5', '{{ (1..5) }}', strictVariables: $strict);
    assertTemplateResult('pass', '{% if (1..5) == expect %}pass{% endif %}', ['expect' => new Range(1, 5)], strictVariables: $strict);

    $assigns = ['test' => '5'];
    assertTemplateResult('1..5', '{{ (1..test) }}', $assigns, strictVariables: $strict);
    assertTemplateResult('5..5', '{{ (test..test) }}', $assigns, strictVariables: $strict);
})->with([
    'default' => false,
    'strict' => true,
]);

test('drop with variable called only once', function (bool $strict) {
    $context = new RenderContext(options: new RenderContextOptions(strictVariables: $strict));
    $context->set('counter', new CounterDrop);

    expect($context->get('counter.count'))->toBe(1);
    expect($context->get('counter.count'))->toBe(2);
    expect($context->get('counter.count'))->toBe(3);
})->with([
    'default' => false,
    'strict' => true,
]);

test('drop with key called only once', function (bool $strict) {
    $context = new RenderContext(options: new RenderContextOptions(strictVariables: $strict));
    $context->set('counter', new CounterDrop);

    expect($context->get('counter["count"]'))->toBe(1);
    expect($context->get('counter["count"]'))->toBe(2);
    expect($context->get('counter["count"]'))->toBe(3);
})->with([
    'default' => false,
    'strict' => true,
]);

test('closure as variable', function (bool $strict) {
    $context = new RenderContext(options: new RenderContextOptions(strictVariables: $strict));
    $context->set('dynamic', fn () => 'hello');

    expect($context->get('dynamic'))->toBe('hello');
})->with([
    'default' => false,
    'strict' => true,
]);

test('nested closure as variable', function (bool $strict) {
    $context = new RenderContext(options: new RenderContextOptions(strictVariables: $strict));
    $context->set('dynamic', ['lambda' => fn () => 'hello']);

    expect($context->get('dynamic.lambda'))->toBe('hello');
})->with([
    'default' => false,
    'strict' => true,
]);

test('array containing closure as variable', function (bool $strict) {
    $context = new RenderContext(options: new RenderContextOptions(strictVariables: $strict));
    $context->set('dynamic', [1, 2, fn () => 'hello', 4, 5]);

    expect($context->get('dynamic[2]'))->toBe('hello');
})->with([
    'default' => false,
    'strict' => true,
]);

test('closure is called once', function (bool $strict) {
    $context = new RenderContext(options: new RenderContextOptions(strictVariables: $strict));
    $global = 0;

    $context->set('callcount', function () use (&$global) {
        $global += 1;

        return $global;
    });

    expect($context->get('callcount'))->toBe(1);
    expect($context->get('callcount'))->toBe(1);
    expect($context->get('callcount'))->toBe(1);
})->with([
    'default' => false,
    'strict' => true,
]);

test('nested closure is called once', function (bool $strict) {
    $context = new RenderContext(options: new RenderContextOptions(strictVariables: $strict));
    $global = 0;

    $context->set('callcount', [
        'lambda' => function () use (&$global) {
            $global += 1;

            return $global;
        },
    ]);

    expect($context->get('callcount.lambda'))->toBe(1);
    expect($context->get('callcount.lambda'))->toBe(1);
    expect($context->get('callcount.lambda'))->toBe(1);
})->with([
    'default' => false,
    'strict' => true,
]);

test('lambda in array is called once', function (bool $strict) {
    $context = new RenderContext(options: new RenderContextOptions(strictVariables: $strict));
    $global = 0;

    $context->set('callcount', [
        1,
        2,
        function () use (&$global) {
            $global += 1;

            return $global;
        },
        4,
        5,
    ]);

    expect($context->get('callcount[2]'))->toBe(1);
    expect($context->get('callcount[2]'))->toBe(1);
    expect($context->get('callcount[2]'))->toBe(1);
})->with([
    'default' => false,
    'strict' => true,
]);

test('access to context from closure', function (bool $strict) {
    $context = new RenderContext(options: new RenderContextOptions(strictVariables: $strict));
    $context->setRegister('magic', 3445392);
    $context->set('closure', fn (RenderContext $context) => $context->getRegister('magic'));

    expect($context->get('closure'))->toBe(3445392);
})->with([
    'default' => false,
    'strict' => true,
]);

test('toLiquid and context at first level', function (bool $strict) {
    $context = new RenderContext(options: new RenderContextOptions(strictVariables: $strict));
    $context->set('category', new Category('foobar'));

    expect($context->get('category'))->toBeInstanceOf(CategoryDrop::class);
    expect(invade($context->get('category'))->context)->toBe($context);
})->with([
    'default' => false,
    'strict' => true,
]);

test('context initialization with a closure in environment', function (bool $strict) {
    $context = new RenderContext(
        data: [
            'test' => fn (RenderContext $c) => $c->get('poutine'),
        ],
        staticData: [
            'poutine' => 'fries',
        ],
        options: new RenderContextOptions(strictVariables: $strict)
    );

    expect($context->get('test'))->toBe('fries');
})->with([
    'default' => false,
    'strict' => true,
]);

test('staticEnvironment has lower priority then environment', function (bool $strict) {
    $context = new RenderContext(
        data: [
            'shadowed' => 'dynamic',
        ],
        staticData: [
            'shadowed' => 'static',
            'unshadowed' => 'static',
        ],
        options: new RenderContextOptions(strictVariables: $strict)
    );

    expect($context->get('shadowed'))->toBe('dynamic');
    expect($context->get('unshadowed'))->toBe('static');
})->with([
    'default' => false,
    'strict' => true,
]);

test('new isolated subcontext does not inherit variables', function (bool $strict) {
    $context = new RenderContext(options: new RenderContextOptions(strictVariables: $strict));
    $context->set('my_variable', 'some value');
    $subContext = $context->newIsolatedSubContext('sub');

    if ($strict) {
        expect($subContext->get('my_variable'))->toBeInstanceOf(UndefinedVariable::class);
    } else {
        expect($subContext->get('my_variable'))->toBeNull();
    }
})->with([
    'default' => false,
    'strict' => true,
]);

test('new isolated subcontext inherit static environments', function (bool $strict) {
    $context = new RenderContext(staticData: ['my_env_value' => 'some value'], options: new RenderContextOptions(strictVariables: $strict));
    $subContext = $context->newIsolatedSubContext('sub');

    expect($subContext->get('my_env_value'))->toBe('some value');
})->with([
    'default' => false,
    'strict' => true,
]);

test('new isolated subcontext does inherit static registers', function (bool $strict) {
    $context = new RenderContext(registers: ['my_register' => 'my value'], options: new RenderContextOptions(strictVariables: $strict));
    $subContext = $context->newIsolatedSubContext('sub');

    expect($subContext->getRegister('my_register'))->toBe('my value');
})->with([
    'default' => false,
    'strict' => true,
]);

test('new isolated subcontext does not inherit non static registers', function (bool $strict) {
    $context = new RenderContext(registers: ['my_register' => 'my value'], options: new RenderContextOptions(strictVariables: $strict));
    $context->setRegister('my_register', 'my alt value');
    $subContext = $context->newIsolatedSubContext('sub');

    expect($subContext->getRegister('my_register'))->toBe('my value');
})->with([
    'default' => false,
    'strict' => true,
]);

test('new isolated subcontext registers do not pollute context', function (bool $strict) {
    $context = new RenderContext(registers: ['my_register' => 'my value'], options: new RenderContextOptions(strictVariables: $strict));
    $subContext = $context->newIsolatedSubContext('sub');
    $subContext->setRegister('my_register', 'my alt value');

    expect($context->getRegister('my_register'))->toBe('my value');
})->with([
    'default' => false,
    'strict' => true,
]);

test('new isolated subcontext inherit resource limits', function (bool $strict) {
    $resourceLimits = new \Keepsuit\Liquid\Render\ResourceLimits;
    $context = new RenderContext(options: new RenderContextOptions(strictVariables: $strict), resourceLimits: $resourceLimits);
    $subContext = $context->newIsolatedSubContext('sub');

    expect($subContext->resourceLimits)->toBe($resourceLimits);
})->with([
    'default' => false,
    'strict' => true,
]);

test('new isolated subcontext inherit environment', function (bool $strict) {
    $environment = \Keepsuit\Liquid\EnvironmentFactory::new()
        ->setFilesystem($fileSystem = new \Keepsuit\Liquid\Tests\Stubs\StubFileSystem)
        ->setStrictVariables($strict)
        ->build();

    $context = new RenderContext(options: new RenderContextOptions(strictVariables: $strict), environment: $environment);
    $subContext = $context->newIsolatedSubContext('sub');

    expect($subContext)
        ->environment->toBe($environment)
        ->environment->fileSystem->toBe($fileSystem);
})->with([
    'default' => false,
    'strict' => true,
]);

test('new isolated subcontext inherit filters', function (bool $strict) {
    $context = \Keepsuit\Liquid\EnvironmentFactory::new()
        ->setStrictVariables($strict)
        ->registerFilters(\Keepsuit\Liquid\Tests\Stubs\TestFilters::class)
        ->build()
        ->newRenderContext();
    $subContext = $context->newIsolatedSubContext('sub');

    expect(parseTemplate('{{ "hi?" | hi }}')->render($subContext))->toBe('hi? hi!');
})->with([
    'default' => false,
    'strict' => true,
]);

test('disabled specified tags', function (bool $strict) {
    $context = new RenderContext(options: new RenderContextOptions(strictVariables: $strict));
    $context->withDisabledTags(['foo', 'bar'], function (RenderContext $context) {
        expect($context)
            ->tagDisabled('foo')->toBe(true)
            ->tagDisabled('bar')->toBe(true)
            ->tagDisabled('unknown')->toBe(false);
    });
})->with([
    'default' => false,
    'strict' => true,
]);

test('disabled nested tags', function (bool $strict) {
    $context = new RenderContext(options: new RenderContextOptions(strictVariables: $strict));
    $context->withDisabledTags(['foo'], function (RenderContext $context) {
        $context->withDisabledTags(['foo'], function (RenderContext $context) {
            expect($context)
                ->tagDisabled('foo')->toBe(true)
                ->tagDisabled('bar')->toBe(false);
        });

        $context->withDisabledTags(['bar'], function (RenderContext $context) {
            expect($context)
                ->tagDisabled('foo')->toBe(true)
                ->tagDisabled('bar')->toBe(true);

            $context->withDisabledTags(['foo'], function (RenderContext $context) {
                expect($context)
                    ->tagDisabled('foo')->toBe(true)
                    ->tagDisabled('bar')->toBe(true);
            });
        });

        expect($context)
            ->tagDisabled('foo')->toBe(true)
            ->tagDisabled('bar')->toBe(false);
    });
})->with([
    'default' => false,
    'strict' => true,
]);

test('has key will not add an error for missing keys', function (bool $strict) {
    $context = new RenderContext(options: new RenderContextOptions(strictVariables: $strict));
    $context->has('unknown');

    expect($context->getErrors())->toBe([]);
})->with([
    'default' => false,
    'strict' => true,
]);
