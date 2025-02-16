<?php

use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\Exceptions\ResourceLimitException;
use Keepsuit\Liquid\Exceptions\UndefinedFilterException;
use Keepsuit\Liquid\Exceptions\UndefinedVariableException;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Render\RenderContextOptions;
use Keepsuit\Liquid\Render\ResourceLimits;

test('assigns persist on same context between renders', function () {
    $template = parseTemplate("{{ foo }}{% assign foo = 'foo' %}{{ foo }}");

    $context = new RenderContext;
    expect($template->render($context))->toBe('foo');
    expect($template->render($context))->toBe('foofoo');
});

test('assigns does not persist on different contexts between renders', function () {
    $template = parseTemplate("{{ foo }}{% assign foo = 'foo' %}{{ foo }}");

    expect($template->render(new RenderContext))->toBe('foo');
    expect($template->render(new RenderContext))->toBe('foo');
});

test('lamdba is called once over multiple renders', function () {
    $template = parseTemplate('{{ number }}');

    $global = 0;
    $context = new RenderContext(
        staticData: [
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
    $template = parseTemplate('0123456789');

    $context = new RenderContext(
        resourceLimits: new ResourceLimits(renderLengthLimit: 9)
    );
    expect(fn () => $template->render($context))->toThrow(ResourceLimitException::class);
    expect($context->resourceLimits->reached())->toBeTrue();

    $context = new RenderContext(
        resourceLimits: new ResourceLimits(renderLengthLimit: 10)
    );
    expect($template->render($context))->toBe('0123456789');
    expect($context->resourceLimits->reached())->toBeFalse();
});

test('resource limits render score', function () {
    $template = parseTemplate('{% for a in (1..10) %} {% for a in (1..10) %} foo {% endfor %} {% endfor %}');
    $context = new RenderContext(
        resourceLimits: new ResourceLimits(renderScoreLimit: 50)
    );
    expect(fn () => $template->render($context))->toThrow(ResourceLimitException::class);
    expect($context->resourceLimits->reached())->toBeTrue();

    $template = parseTemplate('{% for a in (1..100) %} foo {% endfor %}');
    $context = new RenderContext(
        resourceLimits: new ResourceLimits(renderScoreLimit: 50)
    );
    expect(fn () => $template->render($context))->toThrow(ResourceLimitException::class);
    expect($context->resourceLimits->reached())->toBeTrue();

    $context = new RenderContext(
        resourceLimits: new ResourceLimits(renderScoreLimit: 200)
    );
    expect($template->render($context))->toBe(str_repeat(' foo ', 100));
    expect($context->resourceLimits->reached())->toBeFalse();
});

test('resource limits abort rendering after first error', function () {
    $template = parseTemplate('{% for a in (1..100) %} foo1 {% endfor %} bar {% for a in (1..100) %} foo2 {% endfor %}');
    $context = new RenderContext(
        options: new RenderContextOptions(rethrowErrors: false),
        resourceLimits: new ResourceLimits(renderScoreLimit: 50)
    );
    expect(fn () => $template->render($context))->toThrow(ResourceLimitException::class);
    expect($context->resourceLimits->reached())->toBeTrue();
});

test('resource limits get updated even if no limits are set', function () {
    $template = parseTemplate('{% for a in (1..100) %}x{% assign foo = 1 %} {% endfor %}');
    $context = new RenderContext;
    $template->render($context);

    expect($context->resourceLimits)
        ->reached()->toBeFalse()
        ->getAssignScore()->toBeGreaterThan(0)
        ->getRenderScore()->toBeGreaterThan(0);
});

test('render length persists between blocks', function () {
    $template = parseTemplate('{% if true %}aaaa{% endif %}');
    $context = new RenderContext(resourceLimits: new ResourceLimits(renderLengthLimit: 3));
    expect(fn () => $template->render($context))->toThrow(ResourceLimitException::class);
    $context = new RenderContext(resourceLimits: new ResourceLimits(renderLengthLimit: 4));
    expect($template->render($context))->toBe('aaaa');

    $template = parseTemplate('{% if true %}aaaa{% endif %}{% if true %}bbb{% endif %}');
    $context = new RenderContext(resourceLimits: new ResourceLimits(renderLengthLimit: 6));
    expect(fn () => $template->render($context))->toThrow(ResourceLimitException::class);
    $context = new RenderContext(resourceLimits: new ResourceLimits(renderLengthLimit: 7));
    expect($template->render($context))->toBe('aaaabbb');

    $template = parseTemplate('{% if true %}a{% endif %}{% if true %}b{% endif %}{% if true %}a{% endif %}{% if true %}b{% endif %}{% if true %}a{% endif %}{% if true %}b{% endif %}');
    $context = new RenderContext(resourceLimits: new ResourceLimits(renderLengthLimit: 5));
    expect(fn () => $template->render($context))->toThrow(ResourceLimitException::class);
    $context = new RenderContext(resourceLimits: new ResourceLimits(renderLengthLimit: 6));
    expect($template->render($context))->toBe('ababab');
});

test('render length uses number of bytes not characters', function () {
    $template = parseTemplate('{% if true %}すごい{% endif %}');
    $context = new RenderContext(resourceLimits: new ResourceLimits(renderLengthLimit: 8));
    expect(fn () => $template->render($context))->toThrow(ResourceLimitException::class);
    $context = new RenderContext(resourceLimits: new ResourceLimits(renderLengthLimit: 9));
    expect($template->render($context))->toBe('すごい');
});

test('undefined variables', function (bool $strict) {
    $environment = EnvironmentFactory::new()
        ->setRethrowErrors(false)
        ->setStrictVariables($strict)
        ->build();

    $template = parseTemplate('{{x}} {{y}} {{z.a}} {{z.b}} {{z.c.d}}');
    $context = $environment->newRenderContext(
        staticData: [
            'x' => 33,
            'z' => ['a' => 32, 'c' => ['e' => 31]],
        ],
    );

    expect($template->render($context))->toBe('33  32  ');

    if ($strict) {
        expect($template->getErrors())
            ->toHaveCount(3)
            ->{0}->toBeInstanceOf(UndefinedVariableException::class)
            ->{0}->getMessage()->toBe('Variable `y` not found')
            ->{1}->toBeInstanceOf(UndefinedVariableException::class)
            ->{1}->getMessage()->toBe('Variable `z.b` not found')
            ->{2}->toBeInstanceOf(UndefinedVariableException::class)
            ->{2}->getMessage()->toBe('Variable `z.c.d` not found');
    } else {
        expect($template->getErrors())->toBeEmpty();
    }
})->with([
    'strict' => true,
    'default' => false,
]);

test('null value does not throw exception', function (bool $strict) {
    $template = parseTemplate('some{{x}}thing');
    $context = new RenderContext(
        staticData: [
            'x' => null,
        ],
        options: new RenderContextOptions(
            strictVariables: $strict,
            rethrowErrors: false,
        )
    );

    expect($template->render($context))->toBe('something');

    expect($template->getErrors())
        ->toHaveCount(0);
})->with([
    'strict' => true,
    'default' => false,
]);

test('undefined drop method', function (bool $strict) {
    $environment = EnvironmentFactory::new()
        ->setRethrowErrors(false)
        ->setStrictVariables($strict)
        ->build();

    $template = parseTemplate('{{ d.text }} {{ d.undefined }}');
    $context = $environment->newRenderContext(
        staticData: [
            'd' => new \Keepsuit\Liquid\Tests\Stubs\TextDrop,
        ],
    );

    expect($template->render($context))->toBe('text1 ');

    if ($strict) {
        expect($template->getErrors())
            ->toHaveCount(1)
            ->{0}->toBeInstanceOf(UndefinedVariableException::class);
    } else {
        expect($template->getErrors())->toBeEmpty();
    }
})->with([
    'strict' => true,
    'default' => false,
]);

test('undefined drop method throw exception', function (bool $strict) {
    $environment = EnvironmentFactory::new()
        ->setRethrowErrors(true)
        ->setStrictVariables($strict)
        ->build();

    $template = parseTemplate('{{ d.text }} {{ d.undefined }}');
    $context = $environment->newRenderContext(
        staticData: [
            'd' => new \Keepsuit\Liquid\Tests\Stubs\TextDrop,
        ],
    );

    if ($strict) {
        expect(fn () => $template->render($context))->toThrow(UndefinedVariableException::class);
    } else {
        expect($template->render($context))->toBe('text1 ');
    }
})->with([
    'strict' => true,
    'default' => false,
]);

test('undefined filter', function (bool $strict) {
    $environment = EnvironmentFactory::new()
        ->setRethrowErrors(false)
        ->setStrictFilters($strict)
        ->build();

    $template = parseTemplate('{{a}} {{x | upcase | somefilter1 | somefilter2 | capitalize}}', $environment);
    $context = $environment->newRenderContext(
        staticData: [
            'a' => 123,
            'x' => 'foo',
        ],
    );

    if ($strict) {
        expect($template->render($context))->toBe('123 ');

        expect($template->getErrors())
            ->toHaveCount(1)
            ->{0}->toBeInstanceOf(UndefinedFilterException::class);
    } else {
        expect($template->render($context))->toBe('123 Foo');

        expect($template->getErrors())->toBeEmpty();
    }
})->with([
    'strict' => true,
    'default' => false,
]);

test('undefined filter throw exception', function (bool $strict) {
    $environment = EnvironmentFactory::new()
        ->setRethrowErrors(true)
        ->setStrictFilters($strict)
        ->build();

    $template = parseTemplate('{{a}} {{x | upcase | somefilter1 | somefilter2 | capitalize}}');
    $context = $environment->newRenderContext(
        staticData: [
            'a' => 123,
            'x' => 'foo',
        ],
    );

    if ($strict) {
        expect(fn () => $template->render($context))->toThrow(UndefinedFilterException::class);
    } else {
        expect($template->render($context))->toBe('123 Foo');
    }
})->with([
    'strict' => true,
    'default' => false,
]);

test('range literals works as expected', function () {
    assertTemplateResult('1..5', '{% assign foo = (x..y) %}{{ foo }}', ['x' => 1, 'y' => 5]);
    assertTemplateResult('12345', '{% assign nums = (x..y) %}{% for num in nums %}{{ num }}{% endfor %}', ['x' => 1, 'y' => 5]);
});
