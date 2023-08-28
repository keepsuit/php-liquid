<?php

use Keepsuit\Liquid\Parse\ErrorMode;

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

    assertTemplateResult(
        '',
        '{{|test}}',
        errorMode: ErrorMode::Lax
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

test('no error on lax empty filter', function () {
    assertTemplateResult(
        '',
        '{{test |a|b|}}',
        errorMode: ErrorMode::Lax
    );
    assertTemplateResult(
        '',
        '{{test}}',
        errorMode: ErrorMode::Lax
    );
    assertTemplateResult(
        '',
        '{{|test|}}',
        errorMode: ErrorMode::Lax
    );
});

test('meaningless parens error lax', function () {
    assertTemplateResult(
        ' YES ',
        "{% if a == 'foo' or (b == 'bar' and c == 'baz') or false %} YES {% endif %}",
        assigns: ['b' => 'bar', 'c' => 'baz'],
        errorMode: ErrorMode::Lax
    );
})->skip('Lax mode is not implemented yet');

test('unexpected characters silently eat logic lax', function () {
    assertTemplateResult(
        ' YES ',
        '{% if true && false %} YES {% endif %}',
        errorMode: ErrorMode::Lax
    );
    assertTemplateResult(
        '',
        '{% if true || false %} YES {% endif %}',
        errorMode: ErrorMode::Lax
    );
})->skip('Lax mode is not implemented yet');

test('throw exception on invalid tag delimiter', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Unexpected outer \'end\' tag',
        '{% end %}'
    );
});

test('unanchored filter arguments', function () {
    assertTemplateResult(
        'hi',
        "{{ 'hi there' | split$$$:' ' | first }}",
        errorMode: ErrorMode::Lax
    );
    assertTemplateResult(
        'x',
        "{{ 'X' | downcase) }}",
        errorMode: ErrorMode::Lax
    );
    assertTemplateResult(
        'here',
        "{{ 'hi there' | split:\"t\"\" | reverse | first}}",
        errorMode: ErrorMode::Lax
    );
    assertTemplateResult(
        'hi ',
        "{{ 'hi there' | split:\"t\"\" | remove:\"i\" | first}}",
        errorMode: ErrorMode::Lax
    );
})->skip('Lax mode is not implemented yet');

test('inline variables work', function () {
    assertTemplateResult(
        'bar',
        "{% assign 123foo = 'bar' %}{{ 123foo }}",
        errorMode: ErrorMode::Lax
    );
    assertTemplateResult(
        '123',
        "{% assign 123 = 'bar' %}{{ 123 }}",
        errorMode: ErrorMode::Lax
    );
});

test('extra dots in ranges', function () {
    assertTemplateResult(
        '12345',
        '{% for i in (1...5) %}{{ i }}{% endfor %}',
        errorMode: ErrorMode::Lax
    );
})->skip('Lax mode is not implemented yet');

test('blank variable markup', function () {
    assertTemplateResult('', '{{}}');
});

test('lookup on var with literal name', function () {
    $assigns = ['blank' => ['x' => 'result']];
    assertTemplateResult('result', '{{ blank.x }}', $assigns);
    assertTemplateResult('result', "{{ blank['x'] }}", $assigns);
});
