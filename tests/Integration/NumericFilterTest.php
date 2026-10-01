<?php

use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\Exceptions\ArithmeticException;
use Keepsuit\Liquid\Exceptions\InternalException;
use Keepsuit\Liquid\Exceptions\UndefinedFilterException;
use Keepsuit\Liquid\Exceptions\UndefinedVariableException;
use Keepsuit\Liquid\Filters\FiltersProvider;
use Keepsuit\Liquid\Tests\Stubs\StubFileSystem;

class NumericFilterOverride extends FiltersProvider
{
    public function plus(string $input, string $operand): string
    {
        return $input.'|'.$operand;
    }
}

test('numeric filters coerce inputs and operands like Shopify Liquid', function (string $source, string $expected) {
    assertTemplateResult($expected, $source);
})->with([
    ["{{ 'abc' | plus: 1 }}", '1'],
    ['{{ nil | plus: 1 }}', '1'],
    ["{{ '1abc' | plus: 1 }}", '2'],
    ['{{ true | plus: 1 }}', '1'],
    ['{{ false | minus: true }}', '0'],
    ["{{ 'abc' | times: 3 }}", '0'],
    ["{{ 1 | at_least: 'x' }}", '1'],
    ["{{ 1 | at_most: 'x' }}", '0'],
    ['{{ nil | round }}', '0'],
    ["{{ 'x' | abs }}", '0'],
    ['{{ true | ceil }}', '0'],
    ['{{ false | floor }}', '0'],
    ["{{ 4.56 | round: 'abc' }}", '5'],
    ["{{ 4.56 | round: '1.5' }}", '4.6'],
    ['{{ -7 | divided_by: 2 }}', '-4'],
    ['{{ 7 | divided_by: -2 }}', '-4'],
    ['{{ -7 | divided_by: -2 }}', '3'],
    ['{{ 9223372036854775807 | divided_by: 1 }}', '9223372036854775807'],
    ['{{ 9223372036854775807 | ceil }}', '9223372036854775807'],
    ['{{ 9223372036854775807 | floor }}', '9223372036854775807'],
    ['{{ 9223372036854775807 | round }}', '9223372036854775807'],
    ['{{ 7 | modulo: -3 }}', '-2'],
    ['{{ -7 | modulo: 3 }}', '2'],
    ['{{ 7.5 | modulo: 2 }}', '1.5'],
    ['{{ -7.5 | modulo: 2 }}', '0.5'],
    ['{{ 7.5 | modulo: -2 }}', '-0.5'],
    ["{{ 'abc' | divided_by: 1 }}", '0'],
    ['{{ true | modulo: 2 }}', '0'],
    ['{{ 1.0 | divided_by: 0 }}', 'Infinity'],
    ['{{ -1.0 | divided_by: 0 }}', '-Infinity'],
    ['{{ 0.0 | divided_by: 0 }}', 'NaN'],
    ['{{ 1 | divided_by: 0.0 }}', 'Infinity'],
    ['{{ 1.0 | divided_by: 0 | plus: 1 }}', 'Infinity'],
]);

test('every numeric filter accepts nil booleans and non-numeric strings', function (string $filter, string $expected, string $input) {
    assertTemplateResult($expected, "{{ $input | $filter }}");
})->with([
    ['plus: 2', '2'],
    ['minus: 2', '-2'],
    ['times: 2', '0'],
    ['divided_by: 2', '0'],
    ['modulo: 2', '0'],
    ['round', '0'],
    ['abs', '0'],
    ['ceil', '0'],
    ['floor', '0'],
    ['at_least: 2', '2'],
    ['at_most: 2', '0'],
])->with(['nil', 'true', 'false', "'abc'"]);

test('numeric coercion keeps environment options independent', function (bool $strictVariables, bool $strictFilters, bool $rethrowErrors, bool $stream) {
    $environment = EnvironmentFactory::new()
        ->setStrictVariables($strictVariables)
        ->setStrictFilters($strictFilters)
        ->setRethrowErrors($rethrowErrors)
        ->setLazyParsing(false)
        ->build();
    $context = $environment->newRenderContext(data: ['value' => null]);
    $template = $environment->parseString("{{ value | plus: true }}|{{ '1abc' | minus: false }}|{{ 7.5 | modulo: -2 }}|{{ 1.0 | divided_by: 0 }}");

    $output = $stream ? implode('', iterator_to_array($template->stream($context))) : $template->render($context);

    expect($output)->toBe('0|1|-0.5|Infinity')
        ->and($context->getErrors())->toBe([]);
})->with([false, true], [false, true], [false, true], [false, true]);

test('numeric filters report missing variables in inputs and arguments under strict variables', function (string $source, bool $rethrowErrors, bool $stream) {
    $environment = EnvironmentFactory::new()
        ->setStrictVariables(true)
        ->setRethrowErrors($rethrowErrors)
        ->build();
    $context = $environment->newRenderContext();
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
    '{{ missing | plus: 1 }}',
    '{{ 1 | plus: missing }}',
    '{{ 1.5 | round: missing }}',
])->with([false, true], [false, true]);

test('missing variables coerce to zero without strict variables', function () {
    assertTemplateResult('1|1|2', '{{ missing | plus: 1 }}|{{ 1 | plus: missing }}|{{ 1.5 | round: missing }}');
    assertTemplateResult('2', '{{ missing | default: 2 | plus: 0 }}', strictVariables: true);
});

test('integer division and modulo by zero remain Liquid arithmetic errors', function (string $source, bool $rethrowErrors, bool $stream) {
    $environment = EnvironmentFactory::new()->setRethrowErrors($rethrowErrors)->build();
    $context = $environment->newRenderContext();
    $template = $environment->parseString($source);
    $render = fn () => $stream ? implode('', iterator_to_array($template->stream($context))) : $template->render($context);

    if ($rethrowErrors) {
        expect($render)->toThrow(ArithmeticException::class, 'divided by 0');
    } else {
        expect($render())->toBe('Liquid error (line 1): divided by 0');
    }

    expect($context->getErrors())->toHaveCount(1)
        ->and($context->getErrors()[0])->toBeInstanceOf(ArithmeticException::class);
})->with([
    '{{ 1 | divided_by: 0 }}',
    "{{ 1 | divided_by: 'abc' }}",
    '{{ 1 | modulo: 0 }}',
    '{{ 1.5 | modulo: 0 }}',
])->with([false, true], [false, true]);

test('strict filters only controls unknown filters after numeric coercion', function (bool $strictFilters, bool $rethrowErrors) {
    $environment = EnvironmentFactory::new()
        ->setStrictFilters($strictFilters)
        ->setRethrowErrors($rethrowErrors)
        ->build();
    $context = $environment->newRenderContext();
    $template = $environment->parseString("{{ 'abc' | plus: 1 | unknown }}");

    if ($strictFilters && $rethrowErrors) {
        expect(fn () => $template->render($context))->toThrow(UndefinedFilterException::class);
    } else {
        expect($template->render($context))->toBe($strictFilters ? '' : '1');
    }

    expect($context->getErrors())->toHaveCount($strictFilters ? 1 : 0);
})->with([false, true], [false, true]);

test('custom filter overrides retain their original types and PHP coercion', function () {
    $factory = EnvironmentFactory::new()->registerFilters(NumericFilterOverride::class);

    expect(renderTemplate('{{ true | plus: false }}', factory: $factory))->toBe('1|');
    expect(fn () => renderTemplate('{{ nil | plus: 1 }}', factory: $factory))
        ->toThrow(InternalException::class);
});

test('numeric coercion works in partials with either lazy parsing setting', function (bool $lazyParsing, bool $stream) {
    $environment = EnvironmentFactory::new()
        ->setStrictVariables(true)
        ->setStrictFilters(true)
        ->setRethrowErrors(true)
        ->setLazyParsing($lazyParsing)
        ->setFilesystem(new StubFileSystem(['number' => '{{ value | plus: 1 }}']))
        ->build();
    $template = $environment->parseString("{% render 'number', value: nil %}");
    $context = $environment->newRenderContext();

    expect($stream ? implode('', iterator_to_array($template->stream($context))) : $template->render($context))
        ->toBe('1');
})->with([false, true], [false, true]);

test('rounding non-finite numeric results reports a Liquid arithmetic error', function (string $filter, string $input, string $message) {
    expect(fn () => renderTemplate("{{ $input | divided_by: 0 | $filter }}"))
        ->toThrow(ArithmeticException::class, $message);
})->with(['ceil', 'floor', 'round'])->with([
    ['1.0', "Computation results in 'Infinity'"],
    ['-1.0', "Computation results in '-Infinity'"],
    ['0.0', "Computation results in 'NaN' (Not a Number)"],
]);
