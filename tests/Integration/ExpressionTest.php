<?php

use Keepsuit\Liquid\Nodes\Range;

test('keyword literals', function () {
    assertTemplateResult('true', '{{ true }}');
    assertExpressionResult(true, 'true');
});

test('string', function () {
    assertTemplateResult('single quoted', "{{'single quoted'}}");
    assertTemplateResult('double quoted', '{{"double quoted"}}');
    assertTemplateResult('spaced', "{{ 'spaced' }}");
    assertTemplateResult('spaced2', "{{ 'spaced2' }}");
});

test('int', function () {
    assertTemplateResult('456', '{{ 456 }}');
    assertExpressionResult(123, '123');
    assertExpressionResult(12, '012');
});

test('float', function () {
    assertTemplateResult('2.5', '{{ 2.5 }}');
    assertExpressionResult(1.5, '1.5');
});

test('range', function () {
    assertTemplateResult('3..4', '{{ ( 3 .. 4 ) }}');
    assertExpressionResult(new Range(1, 2), '(1..2)');

    assertMatchSyntaxError(
        "Liquid syntax error (line 1): Invalid expression type 'false' in range expression",
        '{{ (false..true) }}',
    );
    assertMatchSyntaxError(
        "Liquid syntax error (line 1): Invalid expression type '(1..2)' in range expression",
        '{{ ((1..2)..3) }}',
    );
});

function assertExpressionResult(mixed $expected, string $markup, ...$assigns): void
{
    $liquid = "{% if expect == $markup %}pass{% else %}got {{ $markup }}{% endif %}";
    assertTemplateResult('pass', $liquid, ['expect' => $expected, ...$assigns]);
}
