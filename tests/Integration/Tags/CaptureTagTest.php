<?php

use Keepsuit\Liquid\Render\RenderContext;

test('capture strict parsing rejects dotted target', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Unexpected token .: "." - Valid syntax: capture <var>',
        '{% capture foo.bar %}x{% endcapture %}');
});

test('capture strict parsing rejects bracketed target', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Unexpected token [: "[" - Valid syntax: capture <var>',
        '{% capture foo[bar] %}x{% endcapture %}');
});

test('capture strict parsing rejects quoted string target', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Expected Identifier, got String - Valid syntax: capture <var>',
        "{% capture 'foo' %}x{% endcapture %}");
});

describe('rendering with template backends', function () {
    test('capture block content in variable', function (bool $compiled) {
        assertTemplateResult('test string', '{% capture var %}test string{% endcapture %}{{var}}', compiled: $compiled);
    });

    test('capture with hyphen in variable name', function (bool $compiled) {
        $source = <<<'LIQUID'
        {% capture this-thing %}Print this-thing{% endcapture -%}
        {{ this-thing -}}
        LIQUID;

        assertTemplateResult('Print this-thing', $source, compiled: $compiled);
    });

    test('capture to variable from outer scope if existing', function (bool $compiled) {
        $source = <<<'LIQUID'
        {% assign var = '' -%}
        {% if true -%}
            {% capture var %}first-block-string{% endcapture -%}
        {% endif -%}
        {% if true -%}
            {% capture var %}test-string{% endcapture -%}
        {% endif -%}
        {{var-}}
        LIQUID;

        assertTemplateResult('test-string', $source, compiled: $compiled);
    });

    test('assigning from capture', function (bool $compiled) {
        $source = <<<'LIQUID'
        {% assign first = '' -%}
        {% assign second = '' -%}
        {% for number in (1..3) -%}
            {% capture first %}{{number}}{% endcapture -%}
            {% assign second = first -%}
        {% endfor -%}
        {{ first }}-{{ second -}}
        LIQUID;

        assertTemplateResult('3-3', $source, compiled: $compiled);
    });

    test('increment assign score by bytes', function (bool $compiled) {
        $context = new RenderContext;
        parseTemplate('{% capture foo %}すごい{% endcapture %}', compiled: $compiled)->render($context);
        expect($context->resourceLimits->getAssignScore())->toBe(9);
    });
})->with('template backends');
