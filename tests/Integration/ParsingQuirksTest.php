<?php

describe('rendering with template backends 1', function () {
    test('parsing css', function (bool $compiled) {
        $text = ' div { font-weight: bold; } ';
        assertTemplateResult($text, $text, compiled: $compiled);
    });
})->with('template backends');

test('throw exception on single close bracket', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Unexpected character }',
        'text {{method} oh nos!');
});

test('throw exception on label and no close bracket', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Variable was not properly terminated with: }}',
        'TEST {{ ');
});

test('throw exception on label and no close bracket percent', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Tag was not properly terminated with: %}',
        'TEST {% ');
});

describe('rendering with template backends 2', function () {
    test('throw exception on empty filter', function (bool $compiled) {
        assertTemplateResult('', '{{test}}', compiled: $compiled);
        assertMatchSyntaxError(
            'Liquid syntax error (line 1): `|` is not a valid expression',
            '{{|test}}', compiled: $compiled);
        assertMatchSyntaxError(
            'Liquid syntax error (line 1): Expected Identifier, got }}',
            '{{test |a|b|}}', compiled: $compiled);
    });
})->with('template backends');

test('meaningless parens error', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Invalid range syntax, correct syntax is (start..end) - Valid syntax: if <condition>',
        "{% if a == 'foo' or (b == 'bar' and c == 'baz') or false %} YES {% endif %}");
});

test('unexpected characters', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Unexpected character &',
        '{% if true && false %} YES {% endif %}');
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Unexpected token |: "|" - Valid syntax: if <condition>',
        '{% if true || false %} YES {% endif %}');
});

test('throw exception on invalid tag delimiter', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Unknown tag \'end\'',
        '{% end %}');
});

describe('rendering with template backends 3', function () {
    test('blank variable markup', function (bool $compiled) {
        assertTemplateResult('', '{{}}', compiled: $compiled);
    });
})->with('template backends');
