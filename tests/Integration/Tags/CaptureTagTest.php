<?php

use Keepsuit\Liquid\Render\RenderContext;

test('capture block content in variable', function () {
    assertTemplateResult('test string', "{% capture 'var' %}test string{% endcapture %}{{var}}");
});

test('capture with hyphen in variable name', function () {
    $source = <<<'LIQUID'
        {% capture this-thing %}Print this-thing{% endcapture -%}
        {{ this-thing -}}
        LIQUID;

    assertTemplateResult('Print this-thing', $source);
});

test('capture to variable from outer scope if existing', function () {
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

    assertTemplateResult('test-string', $source);
});

test('assigning from capture', function () {
    $source = <<<'LIQUID'
        {% assign first = '' -%}
        {% assign second = '' -%}
        {% for number in (1..3) -%}
            {% capture first %}{{number}}{% endcapture -%}
            {% assign second = first -%}
        {% endfor -%}
        {{ first }}-{{ second -}}
        LIQUID;

    assertTemplateResult('3-3', $source);
});

test('increment assign score by bytes', function () {
    $context = new RenderContext;
    parseTemplate('{% capture foo %}すごい{% endcapture %}')->render($context);
    expect($context->resourceLimits->getAssignScore())->toBe(9);
});
