<?php

use Keepsuit\Liquid\Exceptions\ResourceLimitException;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Render\RenderContextOptions;
use Keepsuit\Liquid\Render\ResourceLimits;

test('assign syntax error', function () {
    expect(fn () => renderTemplate('{% assign foo not values %}.'))
        ->toThrow(SyntaxException::class);

    expect(fn () => renderTemplate("{% assign foo = ('X' | downcase) %}"))
        ->toThrow(SyntaxException::class);
});

describe('rendering with template backends', function () {
    test('assign with hyphen in variable name', function (bool $compiled) {
        $source = <<<'LIQUID'
    {% assign this-thing = 'Print this-thing' -%}
    {{ this-thing -}}
    LIQUID;

        assertTemplateResult('Print this-thing', $source, compiled: $compiled);
    });

    test('assigned variable', function (bool $compiled) {
        assertTemplateResult(
            '.foo.',
            '{% assign foo = values %}.{{ foo[0] }}.',
            staticData: ['values' => ['foo', 'bar', 'baz']], compiled: $compiled);

        assertTemplateResult(
            '.bar.',
            '{% assign foo = values %}.{{ foo[1] }}.',
            staticData: ['values' => ['foo', 'bar', 'baz']], compiled: $compiled);
    });

    test('assign preserves values already stored in the active scope', function (bool $compiled) {
        assertTemplateResult(
            'first-second',
            '{% assign first = "first" %}{% assign second = "second" %}{{ first }}-{{ second }}',
            compiled: $compiled);
    });

    test('assigned with filter', function (bool $compiled) {
        assertTemplateResult(
            '.bar.',
            '{% assign foo = values | split: "," %}.{{ foo[1] }}.',
            staticData: ['values' => 'foo,bar,baz'], compiled: $compiled);
    });

    test('expression with whitespace in square brackets', function (bool $compiled) {
        assertTemplateResult(
            'result',
            "{% assign r = a[ 'b' ] %}{{ r }}",
            staticData: ['a' => ['b' => 'result']], compiled: $compiled);
    });

    test('assign score exceeding resource limit', function (bool $compiled) {
        $template = parseTemplate('{% assign foo = 42 %}{% assign bar = 23 %}', compiled: $compiled);

        $context = new RenderContext(options: new RenderContextOptions(rethrowErrors: true), resourceLimits: new ResourceLimits(assignScoreLimit: 1));
        expect(fn () => $template->render($context))->toThrow(ResourceLimitException::class);
        expect($context->resourceLimits->reached())->toBeTrue();

        $context = new RenderContext(options: new RenderContextOptions(rethrowErrors: true), resourceLimits: new ResourceLimits(assignScoreLimit: 2));
        expect($template->render($context))->toBe('');
        expect($context->resourceLimits->reached())->toBeFalse();
        expect($context->resourceLimits->getAssignScore())->toBe(2);
    });

    test('assigned ranges have the same resource score as arrays', function (bool $compiled) {
        $environment = testEnvironmentFactory($compiled)->setRethrowErrors(true)->build();
        $template = testParseString($environment, '{% assign values = (1..3) %}{{ values | join }}');
        $context = $environment->newRenderContext(resourceLimits: new ResourceLimits(assignScoreLimit: 3));
        expect(fn () => $template->render($context))->toThrow(ResourceLimitException::class);

        $context = $environment->newRenderContext(resourceLimits: new ResourceLimits(assignScoreLimit: 4));
        expect($template->render($context))->toBe('1 2 3');
        expect($context->resourceLimits->getAssignScore())->toBe(4);
    });

    test('range assignment limits are checked without materializing the range', function (bool $compiled, int $start, int $end, string $prefix) {
        $environment = testEnvironmentFactory($compiled)->build();
        $template = testParseString($environment, $prefix.'{% assign values = (start..end) %}');
        $context = $environment->newRenderContext(
            data: ['start' => $start, 'end' => $end],
            resourceLimits: new ResourceLimits(assignScoreLimit: 1),
        );

        expect(fn () => $template->render($context))->toThrow(ResourceLimitException::class);
        expect($context->resourceLimits->reached())->toBeTrue();
    })->with([
        'large range' => [1, 10_000_000],
        'integer overflow' => [PHP_INT_MIN, PHP_INT_MAX],
    ])->with(['', '{% assign small = 0 %}']);

    test('nested range assignment scores cannot overflow', function (bool $compiled, bool $rethrowErrors, bool $associative) {
        $environment = testEnvironmentFactory($compiled)->setRethrowErrors($rethrowErrors)->build();
        $template = testParseString($environment, '{% assign values = items %}');
        $range = new \Keepsuit\Liquid\Nodes\Range(PHP_INT_MIN, PHP_INT_MAX);
        $context = $environment->newRenderContext(
            data: ['items' => $associative ? ['range' => $range] : [$range]],
            resourceLimits: new ResourceLimits(assignScoreLimit: 1),
        );

        expect(fn () => $template->render($context))->toThrow(ResourceLimitException::class);
        expect($context->resourceLimits->reached())->toBeTrue();
    })->with([false, true])->with([false, true]);

    test('assign score exceeding resource limit from composite object', function (bool $compiled) {
        $environment = testEnvironmentFactory($compiled)
            ->setRethrowErrors(true)->build();

        $template = testParseString($environment, "{% assign foo = 'aaaa' | split: '' %}");

        $context = $environment->newRenderContext(resourceLimits: new ResourceLimits(assignScoreLimit: 3));
        expect(fn () => $template->render($context))->toThrow(ResourceLimitException::class);
        expect($context->resourceLimits->reached())->toBeTrue();

        $context = $environment->newRenderContext(resourceLimits: new ResourceLimits(assignScoreLimit: 5));
        expect($template->render($context))->toBe('');
        expect($context->resourceLimits->reached())->toBeFalse();
        expect($context->resourceLimits->getAssignScore())->toBe(5);
    });
})->with('template backends');

test('assign strict parsing rejects dotted target', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Expected ==, got . - Valid syntax: assign <var> = <source>',
        '{% assign foo.bar = "x" %}');
});

test('assign strict parsing rejects bracketed target', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Expected ==, got [ - Valid syntax: assign <var> = <source>',
        '{% assign foo[bar] = "x" %}');
});

describe('assignment score with template backends', function () {
    test('assign score of int', function (bool $compiled) {
        expect(assignScoreOf(123, $compiled))->toBe(1);
    });

    test('assign score of string', function (bool $compiled) {
        expect(assignScoreOf('123', $compiled))->toBe(3);
        expect(assignScoreOf('12345', $compiled))->toBe(5);
        expect(assignScoreOf('すごい', $compiled))->toBe(9);
    });

    test('assign score of array', function (bool $compiled) {
        expect(assignScoreOf([], $compiled))->toBe(1);
        expect(assignScoreOf([123], $compiled))->toBe(2);
        expect(assignScoreOf([123, 'abcd'], $compiled))->toBe(6);
        expect(assignScoreOf(['int' => 123], $compiled))->toBe(5);
        expect(assignScoreOf(['int' => 123, 'str' => 'abcd'], $compiled))->toBe(12);
    });

})->with('template backends');

function assignScoreOf(mixed $value, bool $compiled): int
{
    $context = new RenderContext(staticData: ['value' => $value], options: new RenderContextOptions(rethrowErrors: true));
    parseTemplate('{% assign obj = value %}', compiled: $compiled)->render($context);

    return $context->resourceLimits->getAssignScore();
}
