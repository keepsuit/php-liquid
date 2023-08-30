<?php

use Keepsuit\Liquid\Exceptions\ResourceLimitException;
use Keepsuit\Liquid\Exceptions\UndefinedDropMethodException;
use Keepsuit\Liquid\Exceptions\UndefinedFilterException;
use Keepsuit\Liquid\Exceptions\UndefinedVariableException;
use Keepsuit\Liquid\Render\Context;
use Keepsuit\Liquid\Render\ResourceLimits;
use Keepsuit\Liquid\Template;

test('assigns persist on same context between renders', function () {
    $template = Template::parse("{{ foo }}{% assign foo = 'foo' %}{{ foo }}");

    $context = new Context();
    expect($template->render($context))->toBe('foo');
    expect($template->render($context))->toBe('foofoo');
});

test('assigns does not persist on different contexts between renders', function () {
    $template = Template::parse("{{ foo }}{% assign foo = 'foo' %}{{ foo }}");

    expect($template->render(new Context()))->toBe('foo');
    expect($template->render(new Context()))->toBe('foo');
});

test('lamdba is called once over multiple renders', function () {
    $template = Template::parse('{{ number }}');

    $global = 0;
    $context = new Context(
        staticEnvironment: [
            'number' => function () use (&$global) {
                $global += 1;

                return $global;
            },
        ]
    );

    expect($template->render($context))->toBe('1');
    expect($template->render($context))->toBe('1');
});

test('resource limits render length', function () {
    $template = Template::parse('0123456789');

    $context = new Context(
        resourceLimits: new ResourceLimits(renderLengthLimit: 9)
    );
    expect(fn () => $template->render($context))->toThrow(ResourceLimitException::class);
    expect($context->resourceLimits->reached())->toBeTrue();

    $context = new Context(
        resourceLimits: new ResourceLimits(renderLengthLimit: 10)
    );
    expect($template->render($context))->toBe('0123456789');
    expect($context->resourceLimits->reached())->toBeFalse();
});

test('resource limits render score', function () {
    $template = Template::parse('{% for a in (1..10) %} {% for a in (1..10) %} foo {% endfor %} {% endfor %}');
    $context = new Context(
        resourceLimits: new ResourceLimits(renderScoreLimit: 50)
    );
    expect(fn () => $template->render($context))->toThrow(ResourceLimitException::class);
    expect($context->resourceLimits->reached())->toBeTrue();

    $template = Template::parse('{% for a in (1..100) %} foo {% endfor %}');
    $context = new Context(
        resourceLimits: new ResourceLimits(renderScoreLimit: 50)
    );
    expect(fn () => $template->render($context))->toThrow(ResourceLimitException::class);
    expect($context->resourceLimits->reached())->toBeTrue();

    $context = new Context(
        resourceLimits: new ResourceLimits(renderScoreLimit: 200)
    );
    expect($template->render($context))->toBe(str_repeat(' foo ', 100));
    expect($context->resourceLimits->reached())->toBeFalse();
});

test('resource limits abort rendering after first error', function () {
    $template = Template::parse('{% for a in (1..100) %} foo1 {% endfor %} bar {% for a in (1..100) %} foo2 {% endfor %}');
    $context = new Context(
        rethrowExceptions: false,
        resourceLimits: new ResourceLimits(renderScoreLimit: 50)
    );
    expect(fn () => $template->render($context))->toThrow(ResourceLimitException::class);
    expect($context->resourceLimits->reached())->toBeTrue();
});

test('resource limits get updated even if no limits are set', function () {
    $template = Template::parse('{% for a in (1..100) %}x{% assign foo = 1 %} {% endfor %}');
    $context = new Context();
    $template->render($context);

    expect($context->resourceLimits)
        ->reached()->toBeFalse()
        ->getAssignScore()->toBeGreaterThan(0)
        ->getRenderScore()->toBeGreaterThan(0);
});

test('render length persists between blocks', function () {
    $template = Template::parse('{% if true %}aaaa{% endif %}');
    $context = new Context(resourceLimits: new ResourceLimits(renderLengthLimit: 3));
    expect(fn () => $template->render($context))->toThrow(ResourceLimitException::class);
    $context = new Context(resourceLimits: new ResourceLimits(renderLengthLimit: 4));
    expect($template->render($context))->toBe('aaaa');

    $template = Template::parse('{% if true %}aaaa{% endif %}{% if true %}bbb{% endif %}');
    $context = new Context(resourceLimits: new ResourceLimits(renderLengthLimit: 6));
    expect(fn () => $template->render($context))->toThrow(ResourceLimitException::class);
    $context = new Context(resourceLimits: new ResourceLimits(renderLengthLimit: 7));
    expect($template->render($context))->toBe('aaaabbb');

    $template = Template::parse('{% if true %}a{% endif %}{% if true %}b{% endif %}{% if true %}a{% endif %}{% if true %}b{% endif %}{% if true %}a{% endif %}{% if true %}b{% endif %}');
    $context = new Context(resourceLimits: new ResourceLimits(renderLengthLimit: 5));
    expect(fn () => $template->render($context))->toThrow(ResourceLimitException::class);
    $context = new Context(resourceLimits: new ResourceLimits(renderLengthLimit: 6));
    expect($template->render($context))->toBe('ababab');
});

test('render length uses number of bytes not characters', function () {
    $template = Template::parse('{% if true %}すごい{% endif %}');
    $context = new Context(resourceLimits: new ResourceLimits(renderLengthLimit: 8));
    expect(fn () => $template->render($context))->toThrow(ResourceLimitException::class);
    $context = new Context(resourceLimits: new ResourceLimits(renderLengthLimit: 9));
    expect($template->render($context))->toBe('すごい');
});

test('undefined variables', function () {
    $template = Template::parse('{{x}} {{y}} {{z.a}} {{z.b}} {{z.c.d}}');
    $context = new Context(
        staticEnvironment: [
            'x' => 33,
            'z' => ['a' => 32, 'c' => ['e' => 31]],
        ],
        rethrowExceptions: false,
        strictVariables: true,
    );

    expect($template->render($context))->toBe('33  32  ');

    expect($template->getErrors())
        ->toHaveCount(3)
        ->{0}->toBeInstanceOf(UndefinedVariableException::class)
        ->{0}->getMessage()->toBe('Variable `y` not found')
        ->{1}->toBeInstanceOf(UndefinedVariableException::class)
        ->{1}->getMessage()->toBe('Variable `b` not found')
        ->{2}->toBeInstanceOf(UndefinedVariableException::class)
        ->{2}->getMessage()->toBe('Variable `d` not found');
});

test('null value does not throw exception', function () {
    $template = Template::parse('some{{x}}thing');
    $context = new Context(
        staticEnvironment: [
            'x' => null,
        ],
        rethrowExceptions: false,
        strictVariables: true,
    );

    expect($template->render($context))->toBe('something');

    expect($template->getErrors())
        ->toHaveCount(0);
});

test('undefined drop method', function () {
    $template = Template::parse('{{ d.text }} {{ d.undefined }}');
    $context = new Context(
        staticEnvironment: [
            'd' => new \Keepsuit\Liquid\Tests\Stubs\TextDrop(),
        ],
        rethrowExceptions: false,
        strictVariables: true,
    );

    expect($template->render($context))->toBe('text1 ');

    expect($template->getErrors())
        ->toHaveCount(1)
        ->{0}->toBeInstanceOf(UndefinedDropMethodException::class);
});

test('undefined drop method throw exception', function () {
    $template = Template::parse('{{ d.text }} {{ d.undefined }}');
    $context = new Context(
        staticEnvironment: [
            'd' => new \Keepsuit\Liquid\Tests\Stubs\TextDrop(),
        ],
        rethrowExceptions: true,
        strictVariables: true,
    );

    expect(fn () => $template->render($context))->toThrow(UndefinedDropMethodException::class);
});

test('undefined filter', function () {
    $template = Template::parse('{{a}} {{x | upcase | somefilter1 | somefilter2 | downcase}}');
    $context = new Context(
        staticEnvironment: [
            'a' => 123,
            'x' => 'foo',
        ],
        rethrowExceptions: false,
        strictVariables: true,
    );

    expect($template->render($context))->toBe('123 ');

    expect($template->getErrors())
        ->toHaveCount(1)
        ->{0}->toBeInstanceOf(UndefinedFilterException::class);
});

test('undefined filter throw exception', function () {
    $template = Template::parse('{{a}} {{x | upcase | somefilter1 | somefilter2 | downcase}}');
    $context = new Context(
        staticEnvironment: [
            'a' => 123,
            'x' => 'foo',
        ],
        rethrowExceptions: true,
        strictVariables: true,
    );

    expect(fn () => $template->render($context))->toThrow(UndefinedFilterException::class);
});

test('range literals works as expected', function () {
    assertTemplateResult('1..5', '{% assign foo = (x..y) %}{{ foo }}', ['x' => 1, 'y' => 5]);
    assertTemplateResult('12345', '{% assign nums = (x..y) %}{% for num in nums %}{{ num }}{% endfor %}', ['x' => 1, 'y' => 5]);
});