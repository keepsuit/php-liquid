<?php

use Keepsuit\Liquid\Nodes\Range;

describe('rendering with template backends', function () {
    test('keyword literals', function (bool $compiled) {
        assertTemplateResult('true', '{{ true }}', compiled: $compiled);
        assertExpressionResult(true, 'true', compiled: $compiled);
    });

    test('string', function (bool $compiled) {
        assertTemplateResult('single quoted', "{{'single quoted'}}", compiled: $compiled);
        assertTemplateResult('double quoted', '{{"double quoted"}}', compiled: $compiled);
        assertTemplateResult('spaced', "{{ 'spaced' }}", compiled: $compiled);
        assertTemplateResult('spaced2', "{{ 'spaced2' }}", compiled: $compiled);
    });

    test('int', function (bool $compiled) {
        assertTemplateResult('456', '{{ 456 }}', compiled: $compiled);
        assertExpressionResult(123, '123', compiled: $compiled);
        assertExpressionResult(12, '012', compiled: $compiled);
    });

    test('float', function (bool $compiled) {
        assertTemplateResult('2.5', '{{ 2.5 }}', compiled: $compiled);
        assertExpressionResult(1.5, '1.5', compiled: $compiled);
    });

    test('range', function (bool $compiled) {
        assertTemplateResult('3..4', '{{ ( 3 .. 4 ) }}', compiled: $compiled);
        assertExpressionResult(new Range(1, 2), '(1..2)', compiled: $compiled);

        assertMatchSyntaxError(
            "Liquid syntax error (line 1): Invalid expression type 'false' in range expression",
            '{{ (false..true) }}',
            compiled: $compiled);
        assertMatchSyntaxError(
            "Liquid syntax error (line 1): Invalid expression type '(1..2)' in range expression",
            '{{ ((1..2)..3) }}',
            compiled: $compiled);
    });

    test('ranges can be filtered and assigned', function (bool $compiled, string $source, string $expected) {
        assertTemplateResult($expected, $source, compiled: $compiled);
        expect(implode('', iterator_to_array(streamTemplate($source, compiled: $compiled))))->toBe($expected);
    })->with([
        'join' => ["{{ (1..3) | join: ',' }}", '1,2,3'],
        'size' => ['{{ (1..3) | size }}', '3'],
        'reverse' => ["{{ (1..3) | reverse | join: ' ' }}", '3 2 1'],
        'assign' => ['{% assign x = (1..3) %}{{ x | join }}', '1 2 3'],
        'assigned size and endpoints' => ['{% assign x = (1..3) %}{{ x.size }}:{{ x.first }}:{{ x.last }}', '3:1:3'],
        'descending' => ['{{ (3..1) | size }}', '0'],
    ]);
})->with('template backends');

test('bare bracket is not a valid expression', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): `[` is not a valid expression',
        '{{ [foo] }}');
});

function assertExpressionResult(mixed $expected, string $markup, bool $compiled = false, ...$assigns): void
{
    $liquid = "{% if expect == $markup %}pass{% else %}got {{ $markup }}{% endif %}";
    assertTemplateResult('pass', $liquid, ['expect' => $expected, ...$assigns], compiled: $compiled);
}
