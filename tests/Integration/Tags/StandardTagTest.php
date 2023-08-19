<?php

test('no transform', function () {
    assertTemplateResult(
        'this text should come out of the template without change...',
        'this text should come out of the template without change...'
    );
    assertTemplateResult('blah', 'blah');
    assertTemplateResult('<blah>', '<blah>');
    assertTemplateResult('|,.:', '|,.:');
    assertTemplateResult('', '');

    $text = <<<'EOT'
    this shouldnt see any transformation either but has multiple lines
    as you can clearly see here ...';
    EOT;
    assertTemplateResult($text, $text);
});

test('has a block which does nothing', function () {
    assertTemplateResult(
        'the comment block should be removed  .. right?',
        'the comment block should be removed {%comment%} be gone.. {%endcomment%} .. right?'
    );
    assertTemplateResult('', '{%comment%}{%endcomment%}');
    assertTemplateResult('', '{%comment%}{% endcomment %}');
    assertTemplateResult('', '{% comment %}{%endcomment%}');
    assertTemplateResult('', '{% comment %}{% endcomment %}');
    assertTemplateResult('', '{%comment%}comment{%endcomment%}');
    assertTemplateResult('', '{% comment %}comment{% endcomment %}');
    assertTemplateResult('', '{% comment %} 1 {% comment %} 2 {% endcomment %} 3 {% endcomment %}');

    assertTemplateResult('', '{%comment%}{%blabla%}{%endcomment%}');
    assertTemplateResult('', '{% comment %}{% blabla %}{% endcomment %}');
    assertTemplateResult('', '{%comment%}{% endif %}{%endcomment%}');
    assertTemplateResult('', '{% comment %}{% endwhatever %}{% endcomment %}');
    assertTemplateResult('', '{% comment %}{% raw %} {{%%%%}}  }} { {% endcomment %} {% comment {% endraw %} {% endcomment %}');
    assertTemplateResult('', '{% comment %}{% " %}{% endcomment %}');
    assertTemplateResult('', '{% comment %}{%%}{% endcomment %}');

    assertTemplateResult('foobar', 'foo{%comment%}comment{%endcomment%}bar');
    assertTemplateResult('foobar', 'foo{% comment %}comment{% endcomment %}bar');
    assertTemplateResult('foobar', 'foo{%comment%} comment {%endcomment%}bar');
    assertTemplateResult('foobar', 'foo{% comment %} comment {% endcomment %}bar');

    assertTemplateResult('foo  bar', 'foo {%comment%} {%endcomment%} bar');
    assertTemplateResult('foo  bar', 'foo {%comment%}comment{%endcomment%} bar');
    assertTemplateResult('foo  bar', 'foo {%comment%} comment {%endcomment%} bar');

    assertTemplateResult('foobar', 'foo{%comment%} {%endcomment%}bar');
});

test('hyphenated assign', function () {
    assertTemplateResult(
        'a-b:1 a-b:2',
        'a-b:{{a-b}} {%assign a-b = 2 %}a-b:{{a-b}}',
        assigns: ['a-b' => '1']
    );
});

test('assign with colon and spaces', function () {
    assertTemplateResult(
        'var2: 1',
        '{%assign var2 = var["a:b c"].paged %}var2: {{var2}}',
        assigns: ['var' => ['a:b c' => ['paged' => '1']]]
    );
});

test('capture', function () {
    assertTemplateResult(
        'content foo content foo ',
        '{{ var2 }}{% capture var2 %}{{ var }} foo {% endcapture %}{{ var2 }}{{ var2 }}',
        assigns: ['var' => 'content']
    );
});

test('capture detects bad syntax', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Syntax Error in \'capture\' - Valid syntax: capture [var]',
        '{{ var2 }}{% capture %}{{ var }} foo {% endcapture %}{{ var2 }}{{ var2 }}',
        assigns: ['var' => 'content']
    );
});

test('case', function () {
    assertTemplateResult(
        ' its 2 ',
        '{% case condition %}{% when 1 %} its 1 {% when 2 %} its 2 {% endcase %}',
        assigns: ['condition' => 2]
    );
    assertTemplateResult(
        ' its 1 ',
        '{% case condition %}{% when 1 %} its 1 {% when 2 %} its 2 {% endcase %}',
        assigns: ['condition' => 1]
    );
    assertTemplateResult(
        '',
        '{% case condition %}{% when 1 %} its 1 {% when 2 %} its 2 {% endcase %}',
        assigns: ['condition' => 3]
    );

    assertTemplateResult(
        ' hit ',
        '{% case condition %}{% when "string here" %} hit {% endcase %}',
        assigns: ['condition' => 'string here']
    );
    assertTemplateResult(
        '',
        '{% case condition %}{% when "string here" %} hit {% endcase %}',
        assigns: ['condition' => 'bad string here']
    );
});

test('case with else', function () {
    assertTemplateResult(
        ' hit ',
        '{% case condition %}{% when 5 %} hit {% else %} else {% endcase %}',
        assigns: ['condition' => 5]
    );
    assertTemplateResult(
        ' else ',
        '{% case condition %}{% when 5 %} hit {% else %} else {% endcase %}',
        assigns: ['condition' => 6]
    );
    assertTemplateResult(
        ' else ',
        '{% case condition %} {% when 5 %} hit {% else %} else {% endcase %}',
        assigns: ['condition' => 6]
    );
});

test('case on size', function () {
    assertTemplateResult('', '{% case a.size %}{% when 1 %}1{% when 2 %}2{% endcase %}', ['a' => []]);
    assertTemplateResult('1', '{% case a.size %}{% when 1 %}1{% when 2 %}2{% endcase %}', ['a' => [1]]);
    assertTemplateResult('2', '{% case a.size %}{% when 1 %}1{% when 2 %}2{% endcase %}', ['a' => [1, 1]]);
    assertTemplateResult('', '{% case a.size %}{% when 1 %}1{% when 2 %}2{% endcase %}', ['a' => [1, 1, 1]]);
    assertTemplateResult('', '{% case a.size %}{% when 1 %}1{% when 2 %}2{% endcase %}', ['a' => [1, 1, 1, 1]]);
    assertTemplateResult('', '{% case a.size %}{% when 1 %}1{% when 2 %}2{% endcase %}', ['a' => [1, 1, 1, 1, 1]]);
});

test('case on size with else', function () {
    assertTemplateResult(
        'else',
        '{% case a.size %}{% when 1 %}1{% when 2 %}2{% else %}else{% endcase %}',
        assigns: ['a' => []],
    );
    assertTemplateResult(
        '1',
        '{% case a.size %}{% when 1 %}1{% when 2 %}2{% else %}else{% endcase %}',
        assigns: ['a' => [1]],
    );
    assertTemplateResult(
        '2',
        '{% case a.size %}{% when 1 %}1{% when 2 %}2{% else %}else{% endcase %}',
        assigns: ['a' => [1, 1]],
    );
    assertTemplateResult(
        'else',
        '{% case a.size %}{% when 1 %}1{% when 2 %}2{% else %}else{% endcase %}',
        assigns: ['a' => [1, 1, 1]],
    );
    assertTemplateResult(
        'else',
        '{% case a.size %}{% when 1 %}1{% when 2 %}2{% else %}else{% endcase %}',
        assigns: ['a' => [1, 1, 1, 1]],
    );
    assertTemplateResult(
        'else',
        '{% case a.size %}{% when 1 %}1{% when 2 %}2{% else %}else{% endcase %}',
        assigns: ['a' => [1, 1, 1, 1, 1]],
    );
});

test('case on length with else', function () {
    assertTemplateResult(
        'else',
        '{% case a %}{% when true %}true{% when false %}false{% else %}else{% endcase %}',
    );
    assertTemplateResult(
        'false',
        '{% case false %}{% when true %}true{% when false %}false{% else %}else{% endcase %}',
    );
    assertTemplateResult(
        'true',
        '{% case true %}{% when true %}true{% when false %}false{% else %}else{% endcase %}',
    );
    assertTemplateResult(
        'else',
        '{% case NULL %}{% when true %}true{% when false %}false{% else %}else{% endcase %}',
    );
});

test('assign from case', function () {
    $template = <<<'LIQUID'
    {%- case collection.handle -%}
    {%- when 'menswear-jackets' -%}
        {%- assign ptitle = 'menswear' -%}
    {%- when 'menswear-t-shirts' -%}
        {%- assign ptitle = 'menswear' -%}
    {%- else -%}
        {%- assign ptitle = 'womenswear' -%}
    {%- endcase -%}
    {{ ptitle }}
    LIQUID;

    assertTemplateResult('menswear', $template, ['collection' => ['handle' => 'menswear-jackets']]);
    assertTemplateResult('menswear', $template, ['collection' => ['handle' => 'menswear-t-shirts']]);
    assertTemplateResult('womenswear', $template, ['collection' => ['handle' => 'x']]);
    assertTemplateResult('womenswear', $template, ['collection' => ['handle' => 'y']]);
    assertTemplateResult('womenswear', $template, ['collection' => ['handle' => 'z']]);
});

test('case when or', function () {
    $template = '{% case condition %}{% when 1 or 2 or 3 %} its 1 or 2 or 3 {% when 4 %} its 4 {% endcase %}';
    assertTemplateResult(' its 1 or 2 or 3 ', $template, ['condition' => 1]);
    assertTemplateResult(' its 1 or 2 or 3 ', $template, ['condition' => 2]);
    assertTemplateResult(' its 1 or 2 or 3 ', $template, ['condition' => 3]);
    assertTemplateResult(' its 4 ', $template, ['condition' => 4]);
    assertTemplateResult('', $template, ['condition' => 5]);

    $template = '{% case condition %}{% when 1 or "string" or null %} its 1 or 2 or 3 {% when 4 %} its 4 {% endcase %}';
    assertTemplateResult(' its 1 or 2 or 3 ', $template, ['condition' => 1]);
    assertTemplateResult(' its 1 or 2 or 3 ', $template, ['condition' => 'string']);
    assertTemplateResult(' its 1 or 2 or 3 ', $template, ['condition' => null]);
    assertTemplateResult('', $template, ['condition' => 'something else']);
});

test('case when comma', function () {
    $template = '{% case condition %}{% when 1, 2, 3 %} its 1 or 2 or 3 {% when 4 %} its 4 {% endcase %}';
    assertTemplateResult(' its 1 or 2 or 3 ', $template, ['condition' => 1]);
    assertTemplateResult(' its 1 or 2 or 3 ', $template, ['condition' => 2]);
    assertTemplateResult(' its 1 or 2 or 3 ', $template, ['condition' => 3]);
    assertTemplateResult(' its 4 ', $template, ['condition' => 4]);
    assertTemplateResult('', $template, ['condition' => 5]);

    $template = '{% case condition %}{% when 1, "string", null %} its 1 or 2 or 3 {% when 4 %} its 4 {% endcase %}';
    assertTemplateResult(' its 1 or 2 or 3 ', $template, ['condition' => 1]);
    assertTemplateResult(' its 1 or 2 or 3 ', $template, ['condition' => 'string']);
    assertTemplateResult(' its 1 or 2 or 3 ', $template, ['condition' => null]);
    assertTemplateResult('', $template, ['condition' => 'something else']);
});

test('case when comma and blank body', function () {
    // TODO: Remove empty string when body is empty
    assertTemplateResult(
        '  result',
        '{% case condition %}{% when 1, 2 %} {% assign r = "result" %} {% endcase %}{{ r }}',
        assigns: ['condition' => 2]
    );
});

test('assign', function () {
    assertTemplateResult('variable', '{% assign a = "variable"%}{{a}}');
});

test('assign unassigned', function () {
    assertTemplateResult(
        'var2:  var2:content',
        'var2:{{var2}} {%assign var2 = var%} var2:{{var2}}',
        assigns: ['var' => 'content']
    );
});

test('assign an empty string', function () {
    assertTemplateResult('', '{% assign a = ""%}{{a}}');
});

test('assign is global', function () {
    assertTemplateResult('variable', '{%for i in (1..2) %}{% assign a = "variable"%}{% endfor %}{{a}}');
});

test('cycle', function () {
    assertTemplateResult('one', '{%cycle "one", "two"%}');
    assertTemplateResult('one two', '{%cycle "one", "two"%} {%cycle "one", "two"%}');
    assertTemplateResult(' two', '{%cycle "", "two"%} {%cycle "", "two"%}');

    assertTemplateResult('one two one', '{%cycle "one", "two"%} {%cycle "one", "two"%} {%cycle "one", "two"%}');

    assertTemplateResult(
        'text-align: left text-align: right',
        '{%cycle "text-align: left", "text-align: right" %} {%cycle "text-align: left", "text-align: right"%}',
    );
});

test('multiple cycles', function () {
    assertTemplateResult(
        '1 2 1 1 2 3 1',
        '{%cycle 1,2%} {%cycle 1,2%} {%cycle 1,2%} {%cycle 1,2,3%} {%cycle 1,2,3%} {%cycle 1,2,3%} {%cycle 1,2,3%}',
    );
});

test('multiple named', function () {
    assertTemplateResult(
        'one one two two one one',
        '{%cycle 1: "one", "two" %} {%cycle 2: "one", "two" %} {%cycle 1: "one", "two" %} {%cycle 2: "one", "two" %} {%cycle 1: "one", "two" %} {%cycle 2: "one", "two" %}',
    );
});

test('multiple named cycle with name from context', function () {
    assertTemplateResult(
        'one one two two one one',
        '{%cycle var1: "one", "two" %} {%cycle var2: "one", "two" %} {%cycle var1: "one", "two" %} {%cycle var2: "one", "two" %} {%cycle var1: "one", "two" %} {%cycle var2: "one", "two" %}',
        assigns: ['var1' => 1, 'var2' => 2],
    );
});

test('size of array', function () {
    assertTemplateResult(
        'array has 4 elements',
        'array has {{ array.size }} elements',
        assigns: ['array' => [1, 2, 3, 4]],
    );
});

test('size of hash', function () {
    assertTemplateResult(
        'hash has 4 elements',
        'hash has {{ hash.size }} elements',
        assigns: ['hash' => ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4]],
    );
});

test('illegal symbols', function () {
    assertTemplateResult('', '{% if true == empty %}?{% endif %}');
    assertTemplateResult('', '{% if true == null %}?{% endif %}');
    assertTemplateResult('', '{% if empty == true %}?{% endif %}');
    assertTemplateResult('', '{% if null == true %}?{% endif %}');
});

test('ifchanged', function () {
    assertTemplateResult(
        '123',
        '{%for item in array%}{%ifchanged%}{{item}}{% endifchanged %}{%endfor%}',
        assigns: ['array' => [1, 1, 2, 2, 3, 3]]
    );

    assertTemplateResult(
        '1',
        '{%for item in array%}{%ifchanged%}{{item}}{% endifchanged %}{%endfor%}',
        assigns: ['array' => [1, 1, 1, 1]]
    );
});

test('multiline tag', function () {
    assertTemplateResult(
        '0 1 2 3',
        <<<'LIQUID'
        0{%
        for i in (1..3)
        %} {{
        i
        }}{%
        endfor
        %}
        LIQUID
    );
});
