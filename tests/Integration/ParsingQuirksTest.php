<?php

test('parsing css', function () {
    $text = ' div { font-weight: bold; } ';
    assertTemplateResult($text, $text);
});

test('throw exception on single close bracket', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Variable \'{{method}\' was not properly terminated with regexp: }}',
        'text {{method} oh nos!'
    );
});

test('throw exception on label and no close bracket', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Variable \'{{\' was not properly terminated with regexp: }}',
        'TEST {{ '
    );
});

test('throw exception on label and no close bracket percent', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Tag \'{%\' was not properly terminated with regexp: %}',
        'TEST {% '
    );
});

test('throw exception on empty filter', function () {
    assertTemplateResult('', '{{test}}');
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): | is not a valid expression in "{{|test}}"',
        '{{|test}}'
    );
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Expected Identifier, got EndOfString in "{{test |a|b|}}"',
        '{{test |a|b|}}'
    );
});

test('meaningless parens error', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Expected DotDot, got Comparison in "a == \'foo\' or (b == \'bar\' and c == \'baz\') or false"',
        "{% if a == 'foo' or (b == 'bar' and c == 'baz') or false %} YES {% endif %}"
    );
});

test('unexpected characters', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Unexpected character & in "true && false"',
        '{% if true && false %} YES {% endif %}'
    );
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Expected EndOfString, got Pipe in "true || false"',
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
});
