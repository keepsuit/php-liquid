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
        'Liquid syntax error (line 1): Unclosed variable',
        'TEST {{ '
    );
});

test('throw exception on label and no close bracket percent', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Unclosed block',
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
        'Liquid syntax error (line 1): Unexpected outer \'end\' tag',
        '{% end %}'
    );
});

test('blank variable markup', function () {
    assertTemplateResult('', '{{}}');
});

test('lookup on var with literal name', function () {
    $assigns = ['blank' => ['x' => 'result']];
    assertTemplateResult('result', '{{ blank.x }}', $assigns);
    assertTemplateResult('result', "{{ blank['x'] }}", $assigns);
})->skip('var with literal name is not supported');
