<?php

describe('rendering with template backends', function () {
    test('increment', function (bool $compiled) {
        assertTemplateResult('0 1', '{%increment port %} {{ port }}', staticData: ['port' => 10], compiled: $compiled);
        assertTemplateResult(' 0 1 2', '{{port}} {%increment port %} {%increment port%} {{port}}', compiled: $compiled);
        assertTemplateResult(
            '0|0|1|2|1',
            <<<'LIQUID'
        {%- increment port %}|
        {%- increment starboard %}|
        {%- increment port %}|
        {%- increment port %}|
        {%- increment starboard %}
        LIQUID
            , compiled: $compiled);
    });

    test('decrement', function (bool $compiled) {
        assertTemplateResult('-1 -1', '{%decrement port %} {{ port }}', staticData: ['port' => 10], compiled: $compiled);
        assertTemplateResult(' -1 -2 -2', '{{port}} {%decrement port %} {%decrement port%} {{port}}', compiled: $compiled);
        assertTemplateResult(
            '0|1|2|0|3|1|1|3',
            <<<'LIQUID'
        {%- increment starboard %}|
        {%- increment starboard %}|
        {%- increment starboard %}|
        {%- increment port %}|
        {%- increment starboard %}|
        {%- increment port %}|
        {%- decrement port %}|
        {%- decrement starboard %}
        LIQUID
            , compiled: $compiled);
    });

    test('increment stores the next value and shares its counter with decrement', function (bool $compiled, string $source, string $expected) {
        assertTemplateResult($expected, $source, compiled: $compiled);
        expect(implode('', iterator_to_array(streamTemplate($source, compiled: $compiled))))->toBe($expected);
    })->with([
        'next value' => ['{% increment x %}{% increment x %}{{ x }}', '012'],
        'shared counter' => ['{% increment x %} {% decrement x %}', '0 0'],
        'assigned variable is independent' => ['{% assign x = 8 %}{% increment x %}{% decrement x %}{{ x }}', '008'],
    ]);
})->with('template backends');

test('increment strict parsing rejects dotted target', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Unexpected token .: "." - Valid syntax: increment <var>',
        '{% increment foo.bar %}');
});

test('increment strict parsing rejects bracketed target', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Unexpected token [: "[" - Valid syntax: increment <var>',
        '{% increment foo[bar] %}');
});

test('decrement strict parsing rejects dotted target', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Unexpected token .: "." - Valid syntax: decrement <var>',
        '{% decrement foo.bar %}');
});

test('decrement strict parsing rejects bracketed target', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Unexpected token [: "[" - Valid syntax: decrement <var>',
        '{% decrement foo[bar] %}');
});
