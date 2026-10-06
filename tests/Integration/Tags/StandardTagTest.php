<?php

test('capture detects bad syntax', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Unexpected end of template - Valid syntax: capture <var>',
        '{{ var2 }}{% capture %}{{ var }} foo {% endcapture %}{{ var2 }}{{ var2 }}',
        staticData: ['var' => 'content']);
});

test('case strict parsing rejects trailing tokens', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Unexpected token Identifier: "extra" - Valid syntax: case <expression>',
        '{% case condition extra %}{% when 1 %} hit {% endcase %}',
        staticData: ['condition' => 1]);
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Unexpected token Identifier: "extra" - Valid syntax: when <expression> [, <expression>...]',
        '{% case condition %}{% when 1 extra %} hit {% endcase %}',
        staticData: ['condition' => 1]);
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Unexpected end of template - Valid syntax: when <expression> [, <expression>...]',
        '{% case condition %}{% when 1, %} hit {% endcase %}',
        staticData: ['condition' => 1]);
});

test('case rejects when after else and duplicate else', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): A when tag cannot follow an else tag - Valid syntax: when <expression> [, <expression>...]',
        '{% case 1 %}{% else %}else{% when 1 %}one{% endcase %}');
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): A when tag cannot follow an else tag - Valid syntax: when <expression> [, <expression>...]',
        '{% case 1 %}{% when 1 %}one{% else %}else{% when 2 %}two{% endcase %}');
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): A case block can only contain one else tag - Valid syntax: else',
        '{% case 1 %}{% when 1 %}one{% else %}else{% else %}again{% endcase %}');
});

test('cycle strict parsing rejects trailing tokens', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Unexpected token Identifier: "extra" - Valid syntax: cycle [<name>:] <value>[, <value>...]',
        '{% cycle "one", "two" extra %}');
});

describe('rendering with template backends', function () {
    test('no transform', function (bool $compiled) {
        assertTemplateResult(
            'this text should come out of the template without change...',
            'this text should come out of the template without change...', compiled: $compiled);
        assertTemplateResult('blah', 'blah', compiled: $compiled);
        assertTemplateResult('<blah>', '<blah>', compiled: $compiled);
        assertTemplateResult('|,.:', '|,.:', compiled: $compiled);
        assertTemplateResult('', '', compiled: $compiled);

        $text = <<<'EOT'
    this shouldnt see any transformation either but has multiple lines
    as you can clearly see here ...';
    EOT;
        assertTemplateResult($text, $text, compiled: $compiled);
    });

    test('has a block which does nothing', function (bool $compiled) {
        assertTemplateResult(
            'the comment block should be removed  .. right?',
            'the comment block should be removed {%comment%} be gone.. {%endcomment%} .. right?', compiled: $compiled);
        assertTemplateResult('', '{%comment%}{%endcomment%}', compiled: $compiled);
        assertTemplateResult('', '{%comment%}{% endcomment %}', compiled: $compiled);
        assertTemplateResult('', '{% comment %}{%endcomment%}', compiled: $compiled);
        assertTemplateResult('', '{% comment %}{% endcomment %}', compiled: $compiled);
        assertTemplateResult('', '{%comment%}comment{%endcomment%}', compiled: $compiled);
        assertTemplateResult('', '{% comment %}comment{% endcomment %}', compiled: $compiled);
        assertMatchSyntaxError('Liquid syntax error (line 1): Unknown tag \'endcomment\'', '{% comment %} 1 {% comment %} 2 {% endcomment %} 3 {% endcomment %}', compiled: $compiled);

        assertTemplateResult('', '{%comment%}{%blabla%}{%endcomment%}', compiled: $compiled);
        assertTemplateResult('', '{% comment %}{% blabla %}{% endcomment %}', compiled: $compiled);
        assertTemplateResult('', '{%comment%}{% endif %}{%endcomment%}', compiled: $compiled);
        assertTemplateResult('', '{% comment %}{% endwhatever %}{% endcomment %}', compiled: $compiled);
        assertTemplateResult('', '{% comment %}{% " %}{% endcomment %}', compiled: $compiled);
        assertTemplateResult('', '{% comment %}{%%}{% endcomment %}', compiled: $compiled);

        assertTemplateResult('foobar', 'foo{%comment%}comment{%endcomment%}bar', compiled: $compiled);
        assertTemplateResult('foobar', 'foo{% comment %}comment{% endcomment %}bar', compiled: $compiled);
        assertTemplateResult('foobar', 'foo{%comment%} comment {%endcomment%}bar', compiled: $compiled);
        assertTemplateResult('foobar', 'foo{% comment %} comment {% endcomment %}bar', compiled: $compiled);

        assertTemplateResult('foo  bar', 'foo {%comment%} {%endcomment%} bar', compiled: $compiled);
        assertTemplateResult('foo  bar', 'foo {%comment%}comment{%endcomment%} bar', compiled: $compiled);
        assertTemplateResult('foo  bar', 'foo {%comment%} comment {%endcomment%} bar', compiled: $compiled);

        assertTemplateResult('foobar', 'foo{%comment%} {%endcomment%}bar', compiled: $compiled);
    });

    test('hyphenated assign', function (bool $compiled) {
        assertTemplateResult(
            'a-b:1 a-b:2',
            'a-b:{{a-b}} {%assign a-b = 2 %}a-b:{{a-b}}',
            staticData: ['a-b' => '1'], compiled: $compiled);
    });

    test('assign with colon and spaces', function (bool $compiled) {
        assertTemplateResult(
            'var2: 1',
            '{%assign var2 = var["a:b c"].paged %}var2: {{var2}}',
            staticData: ['var' => ['a:b c' => ['paged' => '1']]], compiled: $compiled);
    });

    test('capture', function (bool $compiled) {
        assertTemplateResult(
            'content foo content foo ',
            '{{ var2 }}{% capture var2 %}{{ var }} foo {% endcapture %}{{ var2 }}{{ var2 }}',
            staticData: ['var' => 'content'], compiled: $compiled);
    });

    test('case', function (bool $compiled) {
        assertTemplateResult(
            ' its 2 ',
            '{% case condition %}{% when 1 %} its 1 {% when 2 %} its 2 {% endcase %}',
            staticData: ['condition' => 2], compiled: $compiled);
        assertTemplateResult(
            ' its 1 ',
            '{% case condition %}{% when 1 %} its 1 {% when 2 %} its 2 {% endcase %}',
            staticData: ['condition' => 1], compiled: $compiled);
        assertTemplateResult(
            '',
            '{% case condition %}{% when 1 %} its 1 {% when 2 %} its 2 {% endcase %}',
            staticData: ['condition' => 3], compiled: $compiled);

        assertTemplateResult(
            ' hit ',
            '{% case condition %}{% when "string here" %} hit {% endcase %}',
            staticData: ['condition' => 'string here'], compiled: $compiled);
        assertTemplateResult(
            '',
            '{% case condition %}{% when "string here" %} hit {% endcase %}',
            staticData: ['condition' => 'bad string here'], compiled: $compiled);
    });

    test('case comparisons use Shopify value types and keep first-match semantics', function (bool $compiled) {
        assertTemplateResult('float match', '{% case 5 %}{% when 5.0 %}float match{% else %}no match{% endcase %}', compiled: $compiled);
        assertTemplateResult('no match', '{% case 5 %}{% when "5" %}string match{% else %}no match{% endcase %}', compiled: $compiled);
        assertTemplateResult('no match', '{% case "5" %}{% when 5 %}number match{% else %}no match{% endcase %}', compiled: $compiled);

        $template = '{% case 1 %}{% when 1 %}first{% when 1 %}second{% endcase %}';
        assertTemplateResult('first', $template, compiled: $compiled);
        expect(implode('', iterator_to_array(streamTemplate($template, compiled: $compiled))))->toBe('first');
    });

    test('case with else', function (bool $compiled) {
        assertTemplateResult(
            ' hit ',
            '{% case condition %}{% when 5 %} hit {% else %} else {% endcase %}',
            staticData: ['condition' => 5], compiled: $compiled);
        assertTemplateResult(
            ' else ',
            '{% case condition %}{% when 5 %} hit {% else %} else {% endcase %}',
            staticData: ['condition' => 6], compiled: $compiled);
        assertTemplateResult(
            ' else ',
            '{% case condition %} {% when 5 %} hit {% else %} else {% endcase %}',
            staticData: ['condition' => 6], compiled: $compiled);
    });

    test('case on size', function (bool $compiled) {
        assertTemplateResult('', '{% case a.size %}{% when 1 %}1{% when 2 %}2{% endcase %}', ['a' => []], compiled: $compiled);
        assertTemplateResult('1', '{% case a.size %}{% when 1 %}1{% when 2 %}2{% endcase %}', ['a' => [1]], compiled: $compiled);
        assertTemplateResult('2', '{% case a.size %}{% when 1 %}1{% when 2 %}2{% endcase %}', ['a' => [1, 1]], compiled: $compiled);
        assertTemplateResult('', '{% case a.size %}{% when 1 %}1{% when 2 %}2{% endcase %}', ['a' => [1, 1, 1]], compiled: $compiled);
        assertTemplateResult('', '{% case a.size %}{% when 1 %}1{% when 2 %}2{% endcase %}', ['a' => [1, 1, 1, 1]], compiled: $compiled);
        assertTemplateResult('', '{% case a.size %}{% when 1 %}1{% when 2 %}2{% endcase %}', ['a' => [1, 1, 1, 1, 1]], compiled: $compiled);
    });

    test('case on size with else', function (bool $compiled) {
        assertTemplateResult(
            'else',
            '{% case a.size %}{% when 1 %}1{% when 2 %}2{% else %}else{% endcase %}',
            staticData: ['a' => []],
            compiled: $compiled);
        assertTemplateResult(
            '1',
            '{% case a.size %}{% when 1 %}1{% when 2 %}2{% else %}else{% endcase %}',
            staticData: ['a' => [1]],
            compiled: $compiled);
        assertTemplateResult(
            '2',
            '{% case a.size %}{% when 1 %}1{% when 2 %}2{% else %}else{% endcase %}',
            staticData: ['a' => [1, 1]],
            compiled: $compiled);
        assertTemplateResult(
            'else',
            '{% case a.size %}{% when 1 %}1{% when 2 %}2{% else %}else{% endcase %}',
            staticData: ['a' => [1, 1, 1]],
            compiled: $compiled);
        assertTemplateResult(
            'else',
            '{% case a.size %}{% when 1 %}1{% when 2 %}2{% else %}else{% endcase %}',
            staticData: ['a' => [1, 1, 1, 1]],
            compiled: $compiled);
        assertTemplateResult(
            'else',
            '{% case a.size %}{% when 1 %}1{% when 2 %}2{% else %}else{% endcase %}',
            staticData: ['a' => [1, 1, 1, 1, 1]],
            compiled: $compiled);
    });

    test('case on length with else', function (bool $compiled) {
        assertTemplateResult(
            'else',
            '{% case a %}{% when true %}true{% when false %}false{% else %}else{% endcase %}',
            compiled: $compiled);
        assertTemplateResult(
            'false',
            '{% case false %}{% when true %}true{% when false %}false{% else %}else{% endcase %}',
            compiled: $compiled);
        assertTemplateResult(
            'true',
            '{% case true %}{% when true %}true{% when false %}false{% else %}else{% endcase %}',
            compiled: $compiled);
        assertTemplateResult(
            'else',
            '{% case NULL %}{% when true %}true{% when false %}false{% else %}else{% endcase %}',
            compiled: $compiled);
    });

    test('assign from case', function (bool $compiled) {
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

        assertTemplateResult('menswear', $template, ['collection' => ['handle' => 'menswear-jackets']], compiled: $compiled);
        assertTemplateResult('menswear', $template, ['collection' => ['handle' => 'menswear-t-shirts']], compiled: $compiled);
        assertTemplateResult('womenswear', $template, ['collection' => ['handle' => 'x']], compiled: $compiled);
        assertTemplateResult('womenswear', $template, ['collection' => ['handle' => 'y']], compiled: $compiled);
        assertTemplateResult('womenswear', $template, ['collection' => ['handle' => 'z']], compiled: $compiled);
    });

    test('case when or', function (bool $compiled) {
        $template = '{% case condition %}{% when 1 or 2 or 3 %} its 1 or 2 or 3 {% when 4 %} its 4 {% endcase %}';
        assertTemplateResult(' its 1 or 2 or 3 ', $template, ['condition' => 1], compiled: $compiled);
        assertTemplateResult(' its 1 or 2 or 3 ', $template, ['condition' => 2], compiled: $compiled);
        assertTemplateResult(' its 1 or 2 or 3 ', $template, ['condition' => 3], compiled: $compiled);
        assertTemplateResult(' its 4 ', $template, ['condition' => 4], compiled: $compiled);
        assertTemplateResult('', $template, ['condition' => 5], compiled: $compiled);

        $template = '{% case condition %}{% when 1 or "string" or null %} its 1 or 2 or 3 {% when 4 %} its 4 {% endcase %}';
        assertTemplateResult(' its 1 or 2 or 3 ', $template, ['condition' => 1], compiled: $compiled);
        assertTemplateResult(' its 1 or 2 or 3 ', $template, ['condition' => 'string'], compiled: $compiled);
        assertTemplateResult(' its 1 or 2 or 3 ', $template, ['condition' => null], compiled: $compiled);
        assertTemplateResult('', $template, ['condition' => 'something else'], compiled: $compiled);
    });

    test('case when comma', function (bool $compiled) {
        $template = '{% case condition %}{% when 1, 2, 3 %} its 1 or 2 or 3 {% when 4 %} its 4 {% endcase %}';
        assertTemplateResult(' its 1 or 2 or 3 ', $template, ['condition' => 1], compiled: $compiled);
        assertTemplateResult(' its 1 or 2 or 3 ', $template, ['condition' => 2], compiled: $compiled);
        assertTemplateResult(' its 1 or 2 or 3 ', $template, ['condition' => 3], compiled: $compiled);
        assertTemplateResult(' its 4 ', $template, ['condition' => 4], compiled: $compiled);
        assertTemplateResult('', $template, ['condition' => 5], compiled: $compiled);

        $template = '{% case condition %}{% when 1, "string", null %} its 1 or 2 or 3 {% when 4 %} its 4 {% endcase %}';
        assertTemplateResult(' its 1 or 2 or 3 ', $template, ['condition' => 1], compiled: $compiled);
        assertTemplateResult(' its 1 or 2 or 3 ', $template, ['condition' => 'string'], compiled: $compiled);
        assertTemplateResult(' its 1 or 2 or 3 ', $template, ['condition' => null], compiled: $compiled);
        assertTemplateResult('', $template, ['condition' => 'something else'], compiled: $compiled);
    });

    test('case when comma and blank body', function (bool $compiled) {
        assertTemplateResult(
            'result',
            '{% case condition %}{% when 1, 2 %} {% assign r = "result" %} {% endcase %}{{ r }}',
            staticData: ['condition' => 2], compiled: $compiled);
    });

    test('assign', function (bool $compiled) {
        assertTemplateResult('variable', '{% assign a = "variable"%}{{a}}', compiled: $compiled);
    });

    test('assign unassigned', function (bool $compiled) {
        assertTemplateResult(
            'var2:  var2:content',
            'var2:{{var2}} {%assign var2 = var%} var2:{{var2}}',
            staticData: ['var' => 'content'], compiled: $compiled);
    });

    test('assign an empty string', function (bool $compiled) {
        assertTemplateResult('', '{% assign a = ""%}{{a}}', compiled: $compiled);
    });

    test('assign is global', function (bool $compiled) {
        assertTemplateResult('variable', '{%for i in (1..2) %}{% assign a = "variable"%}{% endfor %}{{a}}', compiled: $compiled);
    });

    test('cycle', function (bool $compiled) {
        assertTemplateResult('one', '{%cycle "one", "two"%}', compiled: $compiled);
        assertTemplateResult('one two', '{%cycle "one", "two"%} {%cycle "one", "two"%}', compiled: $compiled);
        assertTemplateResult(' two', '{%cycle "", "two"%} {%cycle "", "two"%}', compiled: $compiled);

        assertTemplateResult('one two one', '{%cycle "one", "two"%} {%cycle "one", "two"%} {%cycle "one", "two"%}', compiled: $compiled);

        assertTemplateResult(
            'text-align: left text-align: right',
            '{%cycle "text-align: left", "text-align: right" %} {%cycle "text-align: left", "text-align: right"%}',
            compiled: $compiled);
    });

    test('multiple cycles', function (bool $compiled) {
        assertTemplateResult(
            '1 2 1 1 2 3 1',
            '{%cycle 1,2%} {%cycle 1,2%} {%cycle 1,2%} {%cycle 1,2,3%} {%cycle 1,2,3%} {%cycle 1,2,3%} {%cycle 1,2,3%}',
            compiled: $compiled);
    });

    test('multiple named', function (bool $compiled) {
        assertTemplateResult(
            'one one two two one one',
            '{%cycle 1: "one", "two" %} {%cycle 2: "one", "two" %} {%cycle 1: "one", "two" %} {%cycle 2: "one", "two" %} {%cycle 1: "one", "two" %} {%cycle 2: "one", "two" %}',
            compiled: $compiled);
    });

    test('multiple named cycle with name from context', function (bool $compiled) {
        assertTemplateResult(
            'one one two two one one',
            '{%cycle var1: "one", "two" %} {%cycle var2: "one", "two" %} {%cycle var1: "one", "two" %} {%cycle var2: "one", "two" %} {%cycle var1: "one", "two" %} {%cycle var2: "one", "two" %}',
            staticData: ['var1' => 1, 'var2' => 2],
            compiled: $compiled);
    });

    test('cycle evaluates variables and dynamic group names', function (bool $compiled, string $source, array $data, string $expected) {
        assertTemplateResult($expected, $source, data: $data, compiled: $compiled);
        expect(implode('', iterator_to_array(streamTemplate($source, data: $data, compiled: $compiled))))->toBe($expected);
    })->with([
        'variable value' => ["{% cycle n, 'b' %}", ['n' => 5], '5'],
        'repeated tag' => ["{% for i in (1..3) %}{% cycle n, 'b' %}{% endfor %}", ['n' => 5], '5b5'],
        'lookup group' => ["{% cycle group.name: n, 'b' %}{% cycle group.name: n, 'b' %}", ['group' => ['name' => 'a'], 'n' => 5], '5b'],
        'independent anonymous variable tags' => ["{% cycle n, 'b' %}{% cycle n, 'b' %}", ['n' => 5], '55'],
        'evaluated literal groups' => ["{% cycle a: 'x', 'y' %}{% cycle b: 'x', 'y' %}", ['a' => 'same', 'b' => 'same'], 'xy'],
        'array value' => ["{% cycle n, 'b' %}", ['n' => [1, 2]], '12'],
    ]);

    test('cycle reports an undefined selected value in strict variables mode', function (bool $compiled) {
        assertTemplateResult('', '{% cycle missing %}', compiled: $compiled);
        expect(fn () => renderTemplate('{% cycle missing %}', strictVariables: true, compiled: $compiled))
            ->toThrow(\Keepsuit\Liquid\Exceptions\UndefinedVariableException::class);
        assertTemplateResult('ok', "{% cycle 'ok', missing %}", strictVariables: true, compiled: $compiled);
    });

    test('size of array', function (bool $compiled) {
        assertTemplateResult(
            'array has 4 elements',
            'array has {{ array.size }} elements',
            staticData: ['array' => [1, 2, 3, 4]],
            compiled: $compiled);
    });

    test('size of hash', function (bool $compiled) {
        assertTemplateResult(
            'hash has 4 elements',
            'hash has {{ hash.size }} elements',
            staticData: ['hash' => ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4]],
            compiled: $compiled);
    });

    test('size of iterable', function (bool $compiled) {
        assertTemplateResult(
            'array has 4 elements',
            'array has {{ array.size }} elements',
            staticData: ['array' => new \Keepsuit\Liquid\Tests\Stubs\Collection([1, 2, 3, 4])],
            compiled: $compiled);
    });

    test('illegal symbols', function (bool $compiled) {
        assertTemplateResult('', '{% if true == empty %}?{% endif %}', compiled: $compiled);
        assertTemplateResult('', '{% if true == null %}?{% endif %}', compiled: $compiled);
        assertTemplateResult('', '{% if empty == true %}?{% endif %}', compiled: $compiled);
        assertTemplateResult('', '{% if null == true %}?{% endif %}', compiled: $compiled);
    });

    test('ifchanged', function (bool $compiled) {
        assertTemplateResult(
            '123',
            '{%for item in array%}{%ifchanged%}{{item}}{% endifchanged %}{%endfor%}',
            staticData: ['array' => [1, 1, 2, 2, 3, 3]], compiled: $compiled);

        assertTemplateResult(
            '1',
            '{%for item in array%}{%ifchanged%}{{item}}{% endifchanged %}{%endfor%}',
            staticData: ['array' => [1, 1, 1, 1]], compiled: $compiled);
    });

    test('multiline tag', function (bool $compiled) {
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
            , compiled: $compiled);
    });
})->with('template backends');
