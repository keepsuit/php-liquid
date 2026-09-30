<?php

test('increment', function () {
    assertTemplateResult('0 1', '{%increment port %} {{ port }}', staticData: ['port' => 10]);
    assertTemplateResult(' 0 1 2', '{{port}} {%increment port %} {%increment port%} {{port}}');
    assertTemplateResult(
        '0|0|1|2|1',
        <<<'LIQUID'
        {%- increment port %}|
        {%- increment starboard %}|
        {%- increment port %}|
        {%- increment port %}|
        {%- increment starboard %}
        LIQUID
    );
});

test('decrement', function () {
    assertTemplateResult('-1 -1', '{%decrement port %} {{ port }}', staticData: ['port' => 10]);
    assertTemplateResult(' -1 -2 -2', '{{port}} {%decrement port %} {%decrement port%} {{port}}');
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
    );
});

test('increment stores the next value and shares its counter with decrement', function (string $source, string $expected) {
    assertTemplateResult($expected, $source);
    expect(implode('', iterator_to_array(streamTemplate($source))))->toBe($expected);
})->with([
    'next value' => ['{% increment x %}{% increment x %}{{ x }}', '012'],
    'shared counter' => ['{% increment x %} {% decrement x %}', '0 0'],
    'assigned variable is independent' => ['{% assign x = 8 %}{% increment x %}{% decrement x %}{{ x }}', '008'],
]);

test('increment strict parsing rejects dotted target', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Unexpected token .: "." - Valid syntax: increment <var>',
        '{% increment foo.bar %}',
    );
});

test('increment strict parsing rejects bracketed target', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Unexpected token [: "[" - Valid syntax: increment <var>',
        '{% increment foo[bar] %}',
    );
});

test('decrement strict parsing rejects dotted target', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Unexpected token .: "." - Valid syntax: decrement <var>',
        '{% decrement foo.bar %}',
    );
});

test('decrement strict parsing rejects bracketed target', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Unexpected token [: "[" - Valid syntax: decrement <var>',
        '{% decrement foo[bar] %}',
    );
});
