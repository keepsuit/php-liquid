<?php

use Keepsuit\Liquid\Context;
use Keepsuit\Liquid\Range;
use Keepsuit\Liquid\Tests\Stubs\Category;
use Keepsuit\Liquid\Tests\Stubs\CategoryDrop;
use Keepsuit\Liquid\Tests\Stubs\CentsDrop;
use Keepsuit\Liquid\Tests\Stubs\ContextSensitiveDrop;
use Keepsuit\Liquid\Tests\Stubs\CounterDrop;
use Keepsuit\Liquid\Tests\Stubs\HundredCents;

beforeEach(function () {
    $this->context = new Context();
});

test('variables', function () {
    $this->context->set('string', 'string');
    expect($this->context->get('string'))->toBe('string');

    $this->context->set('num', 5);
    expect($this->context->get('num'))->toBe(5);

    $this->context->set('bool', true);
    expect($this->context->get('bool'))->toBe(true);
    $this->context->set('bool', false);
    expect($this->context->get('bool'))->toBe(false);

    $this->context->set('date', new DateTime('2019-01-01'));
    expect($this->context->get('date'))->toBeInstanceOf(DateTime::class);

    $this->context->set('nil', null);
    expect($this->context->get('nil'))->toBe(null);
});

test('variables not existing', function () {
    assertTemplateResult('true', '{% if does_not_exist == nil %}true{% endif %}');
});

test('array size', function () {
    assertTemplateResult(
        'true',
        '{% if numbers.size == 4 %}true{% endif %}',
        ['numbers' => [1, 2, 3, 4]],
    );
    assertTemplateResult(
        'true',
        '{% if numbers.size == 4 %}true{% endif %}',
        ['numbers' => [1 => 1, 2 => 2, 3 => 3, 4 => 4]],
    );
    assertTemplateResult(
        'true',
        '{% if numbers.size == 1000 %}true{% endif %}',
        ['numbers' => [1 => 1, 2 => 2, 3 => 3, 4 => 4, 'size' => 1000]],
    );
});

test('hyphenated variable', function () {
    assertTemplateResult('godz', '{{ oh-my }}', ['oh-my' => 'godz']);
});

test('add filter', function () {
    $context = new Context(filters: [\Keepsuit\Liquid\Tests\Stubs\TestFilters::class]);
    expect($context->applyFilter('hi', 'hi?'))->toBe('hi? hi!');

    $context = new Context();
    expect($context->applyFilter('hi', 'hi?'))->toBe('hi?');
});

test('add item in outer scope', function () {
    $this->context->set('test', 'test');
    $this->context->stack(function () {
        expect($this->context->get('test'))->toBe('test');
    });
    expect($this->context->get('test'))->toBe('test');
});

test('add item in inner scope', function () {
    $this->context->stack(function () {
        $this->context->set('test', 'test');

        expect($this->context->get('test'))->toBe('test');
    });
    expect($this->context->get('test'))->toBeNull();
});

test('hierarchical data', function () {
    $assigns = ['hash' => ['name' => 'tobi']];
    assertTemplateResult('tobi', '{{ hash.name }}', $assigns);
    assertTemplateResult('tobi', '{{ hash["name"] }}', $assigns);
});

test('keywords', function () {
    assertTemplateResult('pass', '{% if true == expect %}pass{% endif %}', ['expect' => true]);
    assertTemplateResult('pass', '{% if false == expect %}pass{% endif %}', ['expect' => false]);
});

test('digits', function () {
    assertTemplateResult('pass', '{% if 100 == expect %}pass{% endif %}', ['expect' => 100]);
    assertTemplateResult('pass', '{% if 100.00 == expect %}pass{% endif %}', ['expect' => 100.00]);
});

test('strings', function () {
    assertTemplateResult('hello!', '{{ "hello!" }}');
    assertTemplateResult('hello!', "{{ 'hello!' }}");
});

test('merge', function () {
    $this->context->merge(['test' => 'test']);
    expect($this->context->get('test'))->toBe('test');

    $this->context->merge(['test' => 'newvalue', 'foo' => 'bar']);
    expect($this->context)
        ->get('test')->toBe('newvalue')
        ->get('foo')->toBe('bar');
});

test('array notation', function () {
    $assigns = ['test' => ['a', 'b']];
    assertTemplateResult('a', '{{ test[0] }}', $assigns);
    assertTemplateResult('b', '{{ test[1] }}', $assigns);
    assertTemplateResult('pass', '{% if test[2] == nil %}pass{% endif %}', $assigns);
});

test('recursive array notation', function () {
    $assigns = ['test' => ['test' => [1, 2, 3, 4, 5]]];
    assertTemplateResult('1', '{{ test.test[0] }}', $assigns);

    $assigns = ['test' => [['test' => 'worked']]];
    assertTemplateResult('worked', '{{ test[0].test }}', $assigns);
});

test('hash to array transition', function () {
    $assigns = [
        'colors' => [
            'Blue' => ['003366', '336699', '6699CC', '99CCFF'],
            'Green' => ['003300', '336633', '669966', '99CC99'],
            'Yellow' => ['CC9900', 'FFCC00', 'FFFF99', 'FFFFCC'],
            'Red' => ['660000', '993333', 'CC6666', 'FF9999'],
        ],
    ];

    assertTemplateResult('003366', '{{ colors.Blue[0] }}', $assigns);
    assertTemplateResult('FF9999', '{{ colors.Red[3] }}', $assigns);
});

test('array first/last', function () {
    $assigns = ['test' => [1, 2, 3, 4, 5]];
    assertTemplateResult('1', '{{ test.first }}', $assigns);
    assertTemplateResult('pass', '{% if test.last == 5 %}pass{% endif %}', $assigns);

    $assigns = ['test' => ['test' => [1, 2, 3, 4, 5]]];
    assertTemplateResult('1', '{{ test.test.first }}', $assigns);
    assertTemplateResult('5', '{{ test.test.last }}', $assigns);

    $assigns = ['test' => [1]];
    assertTemplateResult('1', '{{ test.first }}', $assigns);
    assertTemplateResult('1', '{{ test.last }}', $assigns);
});

test('access hashes with hash notation', function () {
    $assigns = ['products' => ['count' => 5, 'tags' => ['deepsnow', 'freestyle']]];
    assertTemplateResult('5', '{{ products["count"] }}', $assigns);
    assertTemplateResult('deepsnow', '{{ products["tags"][0] }}', $assigns);
    assertTemplateResult('deepsnow', '{{ products["tags"].first }}', $assigns);

    $assigns = ['product' => ['variants' => [['title' => 'draft151cm'], ['title' => 'element151cm']]]];
    assertTemplateResult('draft151cm', '{{ product["variants"][0]["title"] }}', $assigns);
    assertTemplateResult('element151cm', '{{ product["variants"][1]["title"] }}', $assigns);
    assertTemplateResult('draft151cm', '{{ product["variants"].first["title"] }}', $assigns);
    assertTemplateResult('element151cm', '{{ product["variants"].last["title"] }}', $assigns);
});

test('access variable with hash notation', function () {
    assertTemplateResult('baz', '{{ ["foo"] }}', ['foo' => 'baz']);
    assertTemplateResult('baz', '{{ [bar] }}', ['foo' => 'baz', 'bar' => 'foo']);
});

test('access hashes with hash access variables', function () {
    $assigns = [
        'var' => 'tags',
        'nested' => ['var' => 'tags'],
        'products' => ['count' => 5, 'tags' => ['deepsnow', 'freestyle']],
    ];

    assertTemplateResult('deepsnow', '{{ products[var].first }}', $assigns);
    assertTemplateResult('freestyle', '{{ products[nested.var].last }}', $assigns);
});

test('hash notation only for hash access', function () {
    $assigns = ['array' => [1, 2, 3, 4, 5]];
    assertTemplateResult('1', '{{ array.first }}', $assigns);
    assertTemplateResult('pass', '{% if array["first"] == nil %}pass{% endif %}', $assigns);

    assertTemplateResult('Hello', '{{ hash["first"] }}', ['hash' => ['first' => 'Hello']]);
});

test('first can appear in middle of call chain', function () {
    $assigns = ['product' => ['variants' => [['title' => 'draft151cm'], ['title' => 'element151cm']]]];

    assertTemplateResult('draft151cm', '{{ product.variants[0].title }}', $assigns);
    assertTemplateResult('element151cm', '{{ product.variants[1].title }}', $assigns);
    assertTemplateResult('draft151cm', '{{ product.variants.first.title }}', $assigns);
    assertTemplateResult('element151cm', '{{ product.variants.last.title }}', $assigns);
});

test('cents', function () {
    $this->context->merge(['cents' => new HundredCents()]);
    expect($this->context->get('cents'))->toBe(100);
});

test('nested cents', function () {
    $this->context->merge(['cents' => ['amount' => new HundredCents()]]);
    expect($this->context->get('cents.amount'))->toBe(100);

    $this->context->merge(['cents' => ['cents' => ['amount' => new HundredCents()]]]);
    expect($this->context->get('cents.cents.amount'))->toBe(100);
});

test('cents through drop', function () {
    $this->context->merge(['cents' => new CentsDrop()]);
    expect($this->context->get('cents.amount'))->toBe(100);
});

test('nested cents through drop', function () {
    $this->context->merge(['vars' => ['cents' => new CentsDrop()]]);
    expect($this->context->get('vars.cents.amount'))->toBe(100);
});

test('cents through drop nestedly', function () {
    $this->context->merge(['cents' => ['cents' => new CentsDrop()]]);
    expect($this->context->get('cents.cents.amount'))->toBe(100);

    $this->context->merge(['cents' => ['cents' => ['cents' => new CentsDrop()]]]);
    expect($this->context->get('cents.cents.cents.amount'))->toBe(100);
});

test('context from within drop', function () {
    $this->context->merge(['test' => '123', 'vars' => new ContextSensitiveDrop()]);
    expect($this->context->get('vars.test'))->toBe('123');
});

test('nested context from within drop', function () {
    $this->context->merge(['test' => '123', 'vars' => ['local' => new ContextSensitiveDrop()]]);
    expect($this->context->get('vars.local.test'))->toBe('123');
});

test('ranges', function () {
    assertTemplateResult('1..5', '{{ (1..5) }}');
    assertTemplateResult('pass', '{% if (1..5) == expect %}pass{% endif %}', ['expect' => new Range(1, 5)]);

    $assigns = ['test' => '5'];
    assertTemplateResult('1..5', '{{ (1..test) }}', $assigns);
    assertTemplateResult('5..5', '{{ (test..test) }}', $assigns);
});

test('drop with variable called only once', function () {
    $this->context->set('counter', new CounterDrop());

    expect($this->context->get('counter.count'))->toBe(1);
    expect($this->context->get('counter.count'))->toBe(2);
    expect($this->context->get('counter.count'))->toBe(3);
});

test('drop with key called only once', function () {
    $this->context->set('counter', new CounterDrop());

    expect($this->context->get('counter["count"]'))->toBe(1);
    expect($this->context->get('counter["count"]'))->toBe(2);
    expect($this->context->get('counter["count"]'))->toBe(3);
});

test('closure as variable', function () {
    $this->context->set('dynamic', fn () => 'hello');

    expect($this->context->get('dynamic'))->toBe('hello');
});

test('nested closure as variable', function () {
    $this->context->set('dynamic', ['lambda' => fn () => 'hello']);

    expect($this->context->get('dynamic.lambda'))->toBe('hello');
});

test('array containing closure as variable', function () {
    $this->context->set('dynamic', [1, 2, fn () => 'hello', 4, 5]);

    expect($this->context->get('dynamic[2]'))->toBe('hello');
});

test('closure is called once', function () {
    $global = 0;

    $this->context->set('callcount', function () use (&$global) {
        $global += 1;

        return $global;
    });

    expect($this->context->get('callcount'))->toBe(1);
    expect($this->context->get('callcount'))->toBe(1);
    expect($this->context->get('callcount'))->toBe(1);
});

test('nested closure is called once', function () {
    $global = 0;

    $this->context->set('callcount', [
        'lambda' => function () use (&$global) {
            $global += 1;

            return $global;
        },
    ]);

    expect($this->context->get('callcount.lambda'))->toBe(1);
    expect($this->context->get('callcount.lambda'))->toBe(1);
    expect($this->context->get('callcount.lambda'))->toBe(1);
});

test('lambda in array is called once', function () {
    $global = 0;

    $this->context->set('callcount', [
        1,
        2,
        function () use (&$global) {
            $global += 1;

            return $global;
        },
        4,
        5,
    ]);

    expect($this->context->get('callcount[2]'))->toBe(1);
    expect($this->context->get('callcount[2]'))->toBe(1);
    expect($this->context->get('callcount[2]'))->toBe(1);
});

test('access to context from closure', function () {
    $this->context->setRegister('magic', 3445392);
    $this->context->set('closure', fn (Context $context) => $context->getRegister('magic'));

    expect($this->context->get('closure'))->toBe(3445392);
});

test('toLiquid and context at first level', function () {
    $this->context->set('category', new Category('foobar'));

    expect($this->context->get('category'))->toBeInstanceOf(CategoryDrop::class);
    expect(invade($this->context->get('category'))->context)->toBe($this->context);
});

test('context initialization with a closure in environment', function () {
    $context = new Context(
        environment: [
            'test' => fn (Context $c) => $c->get('poutine'),
        ],
        staticEnvironment: [
            'poutine' => 'fries',
        ]
    );

    expect($context->get('test'))->toBe('fries');
});

test('staticEnvironment has lower priority then environment', function () {
    $context = new Context(
        environment: [
            'shadowed' => 'dynamic',
        ],
        staticEnvironment: [
            'shadowed' => 'static',
            'unshadowed' => 'static',
        ]
    );

    expect($context->get('shadowed'))->toBe('dynamic');
    expect($context->get('unshadowed'))->toBe('static');
});

test('new isolated subcontext does not inherit variables', function () {
    $context = new Context();
    $context->set('my_variable', 'some value');
    $subContext = $context->newIsolatedSubContext('sub');

    expect($subContext->get('my_variable'))->toBeNull();
});

test('new isolated subcontext inherit static environments', function () {
    $context = new Context(staticEnvironment: ['my_env_value' => 'some value']);
    $subContext = $context->newIsolatedSubContext('sub');

    expect($subContext->get('my_env_value'))->toBe('some value');
});

test('new isolated subcontext does inherit static registers', function () {
    $context = new Context(registers: ['my_register' => 'my value']);
    $subContext = $context->newIsolatedSubContext('sub');

    expect($subContext->getRegister('my_register'))->toBe('my value');
});

test('new isolated subcontext does not inherit non static registers', function () {
    $context = new Context(registers: ['my_register' => 'my value']);
    $context->setRegister('my_register', 'my alt value');
    $subContext = $context->newIsolatedSubContext('sub');

    expect($subContext->getRegister('my_register'))->toBe('my value');
});

test('new isolated subcontext registers do not pollute context', function () {
    $context = new Context(registers: ['my_register' => 'my value']);
    $subContext = $context->newIsolatedSubContext('sub');
    $subContext->setRegister('my_register', 'my alt value');

    expect($context->getRegister('my_register'))->toBe('my value');
});

test('new isolated subcontext inherit resource limits', function () {
    $resourceLimits = new \Keepsuit\Liquid\ResourceLimits();
    $context = new Context(resourceLimits: $resourceLimits);
    $subContext = $context->newIsolatedSubContext('sub');

    expect($subContext->resourceLimits)->toBe($resourceLimits);
});

test('new isolated subcontext inherit file system', function () {
    $fileSystem = new \Keepsuit\Liquid\Tests\Stubs\StubFileSystem();
    $context = new Context(fileSystem: $fileSystem);
    $subContext = $context->newIsolatedSubContext('sub');

    expect($subContext->fileSystem)->toBe($fileSystem);
});

test('new isolated subcontext inherit filters', function () {
    $context = new Context(filters: [\Keepsuit\Liquid\Tests\Stubs\TestFilters::class]);
    $subContext = $context->newIsolatedSubContext('sub');

    expect(\Keepsuit\Liquid\Template::parse('{{ "hi?" | hi }}')->render($subContext))->toBe('hi? hi!');
});
