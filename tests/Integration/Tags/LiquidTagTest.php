<?php

test('liquid tag', function () {
    assertTemplateResult('1 2 3', <<<'LIQUID'
    {%- liquid
        echo array | join: " "
    -%}
    LIQUID, assigns: ['array' => [1, 2, 3]]);

    assertTemplateResult('1 2 3', <<<'LIQUID'
    {%- liquid
        for value in array
            echo value
            unless forloop.last
                echo " "
            endunless
        endfor
    -%}
    LIQUID, assigns: ['array' => [1, 2, 3]]);

    assertTemplateResult('4 8 12 6', <<<'LIQUID'
    {%- liquid
        for value in array
            assign double_value = value | times: 2
            echo double_value | times: 2
            unless forloop.last
                echo " "
            endunless
        endfor

        echo " "
        echo double_value
    -%}
    LIQUID, assigns: ['array' => [1, 2, 3]]);

    assertTemplateResult('abc', <<<'LIQUID'
    {%- liquid echo "a" -%}
    b
    {%- liquid echo "c" -%}
    LIQUID
    );
});

test('liquid tag errors', function () {
    assertMatchSyntaxError("Liquid syntax error (line 1): Unknown tag 'error'", <<<'LIQUID'
        {%- liquid error no such tag -%}
        LIQUID
    );

    assertMatchSyntaxError("Liquid syntax error (line 7): Unknown tag 'error'", <<<'LIQUID'
        {{ test }}

        {%-
            liquid
            for value in array

                error no such tag
            endfor
        -%}
        LIQUID
    );

    assertMatchSyntaxError("Liquid syntax error (line 2): Unknown tag '!!! the guards are vigilant'", <<<'LIQUID'
        {%- liquid
            !!! the guards are vigilant
        -%}
        LIQUID
    );

    assertMatchSyntaxError("Liquid syntax error (line 4): 'for' tag was never closed", <<<'LIQUID'
    {%- liquid
        for value in array
            echo 'forgot to close the for tag'
    -%}
    LIQUID
    );
});

test('line number is correct after a blank token', function () {
    assertMatchSyntaxError("Liquid syntax error (line 3): Unknown tag 'error'", "{% liquid echo ''\n\n error %}");
    assertMatchSyntaxError("Liquid syntax error (line 3): Unknown tag 'error'", "{% liquid echo ''\n  \n error %}");
});

test('nested liquid tag', function () {
    assertTemplateResult('good', <<<'LIQUID'
    {%- if true -%}
        {%- liquid
            echo "good"
        -%}
    {%- endif -%}
    LIQUID
    );
});

test('cannot open blocks living past a liquid tag', function () {
    assertMatchSyntaxError("Liquid syntax error (line 3): 'if' tag was never closed", <<<'LIQUID'
    {%- liquid
        if true
    -%}
    {%- endif -%}
    LIQUID
    );
});

test('cannot close blocks created before a liquid tag', function () {
    assertMatchSyntaxError("Liquid syntax error (line 3): 'endif' is not a valid delimiter for liquid tags. use %}", <<<'LIQUID'
    {%- if true -%}
    42
    {%- liquid endif -%}
    LIQUID
    );
});

test('liquid tag in raw', function () {
    assertTemplateResult("{% liquid echo 'test' %}", <<<'LIQUID'
    {% raw %}{% liquid echo 'test' %}{% endraw %}
    LIQUID
    );
});
