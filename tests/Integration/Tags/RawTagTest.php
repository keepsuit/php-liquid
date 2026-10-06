<?php

class VerbatimOutputTag extends \Keepsuit\Liquid\Tags\RawTag
{
    public static function tagName(): string
    {
        return 'verbatim';
    }
}

describe('rendering with template backends', function () {
    test('tag in raw', function (bool $compiled) {
        assertTemplateResult(
            '{% comment %} test {% endcomment %}',
            '{% raw %}{% comment %} test {% endcomment %}{% endraw %}',
            compiled: $compiled);
    });

    test('raw preserves inner whitespace while its delimiters trim outer whitespace', function (bool $compiled, string $source, string $expected) {
        assertTemplateResult($expected, $source, compiled: $compiled);
        expect(implode('', iterator_to_array(streamTemplate($source, compiled: $compiled))))->toBe($expected);
    })->with([
        ['{%- raw -%} a {%- endraw -%}', ' a '],
        ["before \n{%- raw -%} a {%- endraw -%}\n after", 'before a after'],
    ]);

    test('raw delimiter combinations preserve opaque multiline content', function (bool $compiled, bool $left, bool $innerLeft, bool $innerRight, bool $right) {
        $body = "\t \n{{ invalid | }} {% unclosed %} \n";
        $source = "before \n{%".($left ? '-' : '').' raw '.($innerLeft ? '-' : '').'%}'
            .$body.'{%'.($innerRight ? '-' : '').' endraw '.($right ? '-' : '')."%}\n after";
        $expected = ($left ? 'before' : "before \n").$body.($right ? 'after' : "\n after");

        assertTemplateResult($expected, $source, compiled: $compiled);
        expect(implode('', iterator_to_array(streamTemplate($source, compiled: $compiled))))->toBe($expected);
    })->with([false, true], [false, true], [false, true], [false, true]);

    test('empty raw content and custom raw body tags preserve their content', function (bool $compiled) {
        assertTemplateResult('', '{%- raw -%}{%- endraw -%}', compiled: $compiled);
        assertTemplateResult(" \n ", "{%- raw -%} \n {%- endraw -%}", compiled: $compiled);
        assertTemplateResult(' {{ invalid | }} ', '{%- verbatim -%} {{ invalid | }} {%- endverbatim -%}',
            factory: \Keepsuit\Liquid\EnvironmentFactory::new()->registerTag(VerbatimOutputTag::class), compiled: $compiled);
    });

    test('output in raw', function (bool $compiled) {
        assertTemplateResult('>{{ test }}<', '> {%- raw -%}{{ test }}{%- endraw -%} <', compiled: $compiled);
        assertTemplateResult('> inner  <', '> {%- raw -%} inner {%- endraw %} <', compiled: $compiled);
        assertTemplateResult('> inner <', '> {%- raw -%} inner {%- endraw -%} <', compiled: $compiled);
        assertTemplateResult('{Hello}', '{% raw %}{{% endraw %}Hello{% raw %}}{% endraw %}', compiled: $compiled);
    });

    test('open tag in raw', function (bool $compiled) {
        assertTemplateResult(' Foobar {% invalid ', '{% raw %} Foobar {% invalid {% endraw %}', compiled: $compiled);
        assertTemplateResult(' Foobar invalid %} ', '{% raw %} Foobar invalid %} {% endraw %}', compiled: $compiled);
        assertTemplateResult(' Foobar {{ invalid ', '{% raw %} Foobar {{ invalid {% endraw %}', compiled: $compiled);
        assertTemplateResult(' Foobar invalid }} ', '{% raw %} Foobar invalid }} {% endraw %}', compiled: $compiled);
        assertTemplateResult(' Foobar {% invalid {% {% endraw ', '{% raw %} Foobar {% invalid {% {% endraw {% endraw %}', compiled: $compiled);
        assertTemplateResult(' Foobar {% {% {% ', '{% raw %} Foobar {% {% {% {% endraw %}', compiled: $compiled);
        assertTemplateResult(' test {% raw %} {% endraw %}', '{% raw %} test {% raw %} {% {% endraw %}endraw %}', compiled: $compiled);
        assertTemplateResult(' Foobar {{ invalid 1', '{% raw %} Foobar {{ invalid {% endraw %}{{ 1 }}', compiled: $compiled);
        assertTemplateResult(' Foobar {% foo {% bar %}', '{% raw %} Foobar {% foo {% bar %}{% endraw %}', compiled: $compiled);
    });
})->with('template backends');

test('invalid  raw', function () {
    assertMatchSyntaxError('Liquid syntax error (line 1): \'raw\' tag was never closed', '{% raw %} foo');
    assertMatchSyntaxError('Liquid syntax error (line 1): Unexpected character }', '{% raw } foo {% endraw %}');
    assertMatchSyntaxError('Liquid syntax error (line 1): Unexpected character }', '{% raw } foo %}{% endraw %}');
});

test('access raw tag body', function () {
    $content = <<<'EOF'
    {% if true %}
    true
    {% else %}
    false
    {% endif %}
    EOF;

    $template = <<<LIQUID
    {% raw %}$content{% endraw %}
    LIQUID;

    $template = parseSource($template);
    $rawTag = $template->root->body->children()[0] ?? null;

    expect($rawTag)
        ->toBeInstanceOf(\Keepsuit\Liquid\Tags\RawTag::class)
        ->getBody()->toBeInstanceOf(\Keepsuit\Liquid\Nodes\Raw::class)
        ->getBody()->value->toBe($content);
});
