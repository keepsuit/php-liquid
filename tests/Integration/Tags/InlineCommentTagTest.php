<?php

test('inline comments returns nothing', function () {
    assertTemplateResult('', '{%- # this is an inline comment -%}');
    assertTemplateResult('', '{%-# this is an inline comment -%}');
    assertTemplateResult('', '{% # this is an inline comment %}');
    assertTemplateResult('', '{%# this is an inline comment %}');
});

test('inline comment does not require a space after the pound sign', function () {
    assertTemplateResult('', '{%#this is an inline comment%}');
});

test('liquid inline comment returns nothing', function () {
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
    LIQUID);
});

test('inline comment can be written on multiple lines', function () {
    assertTemplateResult('', <<<'LIQUID'
        {%
            ###############################
            # This is a comment
            # across multiple lines
            ###############################
        %}
        LIQUID
    );
});

test('inline comment can be written on multiple lines inside liquid tag', function () {
    assertTemplateResult('', <<<'LIQUID'
        {%- liquid
            ######################################
            # We support comments like this too. #
            ######################################
        -%}
        LIQUID
    );
});

test('inline comment does not support nested tags', function () {
    assertMatchSyntaxError('Liquid syntax error (line 1): Unexpected token type: %}', "{%- # {% echo 'hello world' %} -%}");
});
