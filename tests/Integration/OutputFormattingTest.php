<?php

use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\Tests\Stubs\StubFileSystem;

test('float output matches Shopify Liquid', function (string $source, string $expected) {
    assertTemplateResult($expected, $source);
    expect(implode('', iterator_to_array(streamTemplate($source))))->toBe($expected);
})->with([
    ['{{ 1.0 }}', '1.0'],
    ['{{ 100.0 }}', '100.0'],
    ['{{ 2 | times: 1.5 }}', '3.0'],
    ['{{ 5 | times: 1.0 }}', '5.0'],
    ['{{ 5.0 | abs }}', '5.0'],
    ['{{ 3.14159265358979 }}', '3.14159265358979'],
    ['{{ 4.56 | round }}', '5'],
    ['{{ 4.56 | round: 0 | times: 2 }}', '10'],
    ['{{ 4.56 | round: 1 }}', '4.6'],
    ['{{ 1.0 | round: 1 }}', '1.0'],
    ['{{ 15.0 | round: -1 }}', '20'],
]);

// Reference strings captured from Shopify Liquid 5.13.0 rendering {{ value }}.
test('float values preserve Ruby notation and shortest round trip precision', function (float $value, string $expected) {
    assertTemplateResult($expected, '{{ value }}', data: ['value' => $value]);
    expect(implode('', iterator_to_array(streamTemplate('{{ value }}', data: ['value' => $value]))))->toBe($expected);
})->with([
    [0.0, '0.0'],
    [-0.0, '-0.0'],
    [0.0001, '0.0001'],
    [0.00001, '1.0e-05'],
    [-0.00001, '-1.0e-05'],
    [1e14, '100000000000000.0'],
    [1e15, '1.0e+15'],
    [1e23, '1.0e+23'],
    [2289752067151709.5, '2289752067151709.5'],
    [-2107702241013736.25, '-2107702241013736.2'],
    [1.2345678901234567, '1.2345678901234567'],
    [0.1 + 0.2, '0.30000000000000004'],
    [PHP_FLOAT_MIN, '2.2250738585072014e-308'],
    [PHP_FLOAT_MAX, '1.7976931348623157e+308'],
    [5e-324, '5.0e-324'],
    [INF, 'Infinity'],
    [-INF, '-Infinity'],
    [NAN, 'NaN'],
]);

test('float string conversions share output formatting', function () {
    assertTemplateResult('1.0|1.0!|!1.0|1.0,3.14159265358979|1.0 2.0',
        "{{ value }}|{{ value | append: '!' }}|{{ value | prepend: '!' }}|{{ values | join: ',' }}|{{ nested | join }}",
        data: ['value' => 1.0, 'values' => [1.0, 3.14159265358979], 'nested' => [[1.0, 2.0]]]);
});

test('arrays and generators render floats consistently', function (bool $stream) {
    $environment = EnvironmentFactory::new()->build();
    $context = $environment->newRenderContext(data: [
        'values' => [1.0, [2.0]],
        'chunks' => (function () {
            yield 1.0;
            yield [2.0];
        })(),
    ]);
    $template = $environment->parseString('{{ values }}|{{ chunks }}');

    expect($stream ? implode('', iterator_to_array($template->stream($context))) : $template->render($context))
        ->toBe('1.02.0|1.02.0');
})->with([false, true]);

test('float formatting is independent of numeric locale', function () {
    $previousLocale = setlocale(LC_NUMERIC, 0);

    try {
        setlocale(LC_NUMERIC, 'it_IT.UTF-8', 'fr_FR.UTF-8', 'de_DE.UTF-8');

        assertTemplateResult('3.14159265358979|3.14159265358979|3.14159265358979',
            "{{ value }}|{{ value | append: '' }}|{{ values | join }}",
            data: ['value' => 3.14159265358979, 'values' => [3.14159265358979]]);
    } finally {
        setlocale(LC_NUMERIC, $previousLocale);
    }
});

test('output formatting keeps environment options independent in partials', function (bool $options, bool $stream) {
    $source = '{{ 2 | times: 1.5 }}|{%- raw -%} a {%- endraw -%}|{% tablerow i in items cols:2 %}{{ i }}{% endtablerow %}';
    $environment = EnvironmentFactory::new()
        ->setStrictVariables($options)
        ->setStrictFilters($options)
        ->setRethrowErrors($options)
        ->setLazyParsing($options)
        ->setFilesystem(new StubFileSystem(partials: ['p' => $source]))
        ->build();
    $template = $environment->parseString("{% render 'p', items: items %}");
    $context = $environment->newRenderContext(data: ['items' => [1.0, 2.0, 3.0]]);

    $output = $stream ? implode('', iterator_to_array($template->stream($context))) : $template->render($context);

    expect($output)->toBe("3.0| a |<tr class=\"row1\">\n<td class=\"col1\">1.0</td><td class=\"col2\">2.0</td></tr>\n<tr class=\"row2\"><td class=\"col1\">3.0</td></tr>\n")
        ->and($context->getErrors())->toBe([]);
})->with([false, true], [false, true]);
