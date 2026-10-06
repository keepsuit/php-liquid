<?php

describe('rendering with template backends', function () {
    test('inline comments returns nothing', function (bool $compiled) {
        assertTemplateResult('', '{%- # this is an inline comment -%}', compiled: $compiled);
        assertTemplateResult('', '{%-# this is an inline comment -%}', compiled: $compiled);
        assertTemplateResult('', '{% # this is an inline comment %}', compiled: $compiled);
        assertTemplateResult('', '{%# this is an inline comment %}', compiled: $compiled);
    });

    test('inline comment does not require a space after the pound sign', function (bool $compiled) {
        assertTemplateResult('', '{%#this is an inline comment%}', compiled: $compiled);
    });

    test('liquid inline comment returns nothing', function (bool $compiled) {
        assertTemplateResult('Hey there, how are you doing today?', <<<'LIQUID'
    {%- liquid
        # This is how you'd write a block comment in a liquid tag.
        # It looks a lot like what you'd have in ruby.

        # You can use it as inline documentation in your
        # liquid blocks to explain why you're doing something.
        echo "Hey there, "

        # It won't affect the output.
        echo "how are you doing today?"
    -%}
    LIQUID, compiled: $compiled);
    });

    test('inline comment can be written on multiple lines', function (bool $compiled) {
        assertTemplateResult('', <<<'LIQUID'
        {%
            ###############################
            # This is a comment
            # across multiple lines
            ###############################
        %}
        LIQUID
            , compiled: $compiled);
    });

    test('inline comment can be written on multiple lines inside liquid tag', function (bool $compiled) {
        assertTemplateResult('', <<<'LIQUID'
        {%- liquid
            ######################################
            # We support comments like this too. #
            ######################################
        -%}
        LIQUID
            , compiled: $compiled);
    });

    test('inline comment does not support nested tags', function (bool $compiled) {
        assertTemplateResult(' -%}', "{%- # {% echo 'hello world' %} -%}", compiled: $compiled);
    });

    test('inline comment keeps trim markers working after trailing whitespace', function (bool $compiled) {
        assertTemplateResult('after', "{%- # this is an inline comment   -%}\nafter", compiled: $compiled);
    });
})->with('template backends');
