<?php

test('parsing css', function () {
    $text = ' div { font-weight: bold; } ';
    assertTemplateResult($text, $text);
});

test('throw exception on single close bracket', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Unexpected character }',
        'text {{method} oh nos!'
    );
});

test('throw exception on label and no close bracket', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Variable was not properly terminated with: }}',
        'TEST {{ '
    );
});

test('throw exception on label and no close bracket percent', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Tag was not properly terminated with: %}',
        'TEST {% '
    );
});

test('throw exception on empty filter', function () {
    assertTemplateResult('', '{{test}}');
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): | is not a valid expression',
        '{{|test}}'
    );
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Expected Identifier, got }}',
        '{{test |a|b|}}'
    );
});

test('meaningless parens error', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Invalid range syntax, correct syntax is (start..end)',
        "{% if a == 'foo' or (b == 'bar' and c == 'baz') or false %} YES {% endif %}"
    );
});

test('unexpected characters', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Unexpected character &',
        '{% if true && false %} YES {% endif %}'
    );
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Unexpected token |: "|"',
        '{% if true || false %} YES {% endif %}'
    );
});

test('throw exception on invalid tag delimiter', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Unknown tag \'end\'',
        '{% end %}'
    );
});

test('blank variable markup', function () {
    assertTemplateResult('', '{{}}');
});
