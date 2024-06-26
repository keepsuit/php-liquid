<?php

use Keepsuit\Liquid\Exceptions\ResourceLimitException;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Render\ResourceLimits;
use Keepsuit\Liquid\TemplateFactory;

test('assign with hyphen in variable name', function () {
    $source = <<<'LIQUID'
    {% assign this-thing = 'Print this-thing' -%}
    {{ this-thing -}}
    LIQUID;

    assertTemplateResult('Print this-thing', $source);
});

test('assigned variable', function () {
    assertTemplateResult(
        '.foo.',
        '{% assign foo = values %}.{{ foo[0] }}.',
        assigns: ['values' => ['foo', 'bar', 'baz']]
    );

    assertTemplateResult(
        '.bar.',
        '{% assign foo = values %}.{{ foo[1] }}.',
        assigns: ['values' => ['foo', 'bar', 'baz']]
    );
});

test('assigned with filter', function () {
    assertTemplateResult(
        '.bar.',
        '{% assign foo = values | split: "," %}.{{ foo[1] }}.',
        assigns: ['values' => 'foo,bar,baz']
    );
});

test('assign syntax error', function () {
    expect(fn () => renderTemplate('{% assign foo not values %}.'))
        ->toThrow(SyntaxException::class, 'assign');

    expect(fn () => renderTemplate("{% assign foo = ('X' | downcase) %}"))
        ->toThrow(SyntaxException::class, 'assign');
});

test('expression with whitespace in square brackets', function () {
    assertTemplateResult(
        'result',
        "{% assign r = a[ 'b' ] %}{{ r }}",
        assigns: ['a' => ['b' => 'result']]
    );
});

test('assign score exceeding resource limit', function () {
    $template = parseTemplate('{% assign foo = 42 %}{% assign bar = 23 %}');

    $context = new RenderContext(rethrowExceptions: true, resourceLimits: new ResourceLimits(assignScoreLimit: 1));
    expect(fn () => $template->render($context))->toThrow(ResourceLimitException::class);
    expect($context->resourceLimits->reached())->toBeTrue();

    $context = new RenderContext(rethrowExceptions: true, resourceLimits: new ResourceLimits(assignScoreLimit: 2));
    expect($template->render($context))->toBe('');
    expect($context->resourceLimits->reached())->toBeFalse();
    expect($context->resourceLimits->getAssignScore())->toBe(2);
});

test('assign score exceeding resource limit from composite object', function () {
    $factory = TemplateFactory::new()
        ->setRethrowExceptions();

    $template = parseTemplate("{% assign foo = 'aaaa' | split: '' %}", factory: $factory);

    $factory->setResourceLimits(new ResourceLimits(assignScoreLimit: 3));
    $context = $factory->newRenderContext();
    expect(fn () => $template->render($context))->toThrow(ResourceLimitException::class);
    expect($context->resourceLimits->reached())->toBeTrue();

    $factory->setResourceLimits(new ResourceLimits(assignScoreLimit: 5));
    $context = $factory->newRenderContext();
    expect($template->render($context))->toBe('');
    expect($context->resourceLimits->reached())->toBeFalse();
    expect($context->resourceLimits->getAssignScore())->toBe(5);
});

test('assign score of int', function () {
    expect(assignScoreOf(123))->toBe(1);
});

test('assign score of string', function () {
    expect(assignScoreOf('123'))->toBe(3);
    expect(assignScoreOf('12345'))->toBe(5);
    expect(assignScoreOf('すごい'))->toBe(9);
});

test('assign score of array', function () {
    expect(assignScoreOf([]))->toBe(1);
    expect(assignScoreOf([123]))->toBe(2);
    expect(assignScoreOf([123, 'abcd']))->toBe(6);
    expect(assignScoreOf(['int' => 123]))->toBe(5);
    expect(assignScoreOf(['int' => 123, 'str' => 'abcd']))->toBe(12);
});

function assignScoreOf(mixed $value): int
{
    $context = new RenderContext(rethrowExceptions: true, staticEnvironment: ['value' => $value]);
    parseTemplate('{% assign obj = value %}')->render($context);

    return $context->resourceLimits->getAssignScore();
}
