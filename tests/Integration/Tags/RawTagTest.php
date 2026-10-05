<?php

class VerbatimOutputTag extends \Keepsuit\Liquid\Tags\RawTag
{
    public static function tagName(): string
    {
        return 'verbatim';
    }
}

test('tag in raw', function () {
    assertTemplateResult(
        '{% comment %} test {% endcomment %}',
        '{% raw %}{% comment %} test {% endcomment %}{% endraw %}',
    );
});

test('raw preserves inner whitespace while its delimiters trim outer whitespace', function (string $source, string $expected) {
    assertTemplateResult($expected, $source);
    expect(implode('', iterator_to_array(streamTemplate($source))))->toBe($expected);
})->with([
    ['{%- raw -%} a {%- endraw -%}', ' a '],
    ["before \n{%- raw -%} a {%- endraw -%}\n after", 'before a after'],
]);

test('raw delimiter combinations preserve opaque multiline content', function (bool $left, bool $innerLeft, bool $innerRight, bool $right) {
    $body = "\t \n{{ invalid | }} {% unclosed %} \n";
    $source = "before \n{%".($left ? '-' : '').' raw '.($innerLeft ? '-' : '').'%}'
        .$body.'{%'.($innerRight ? '-' : '').' endraw '.($right ? '-' : '')."%}\n after";
    $expected = ($left ? 'before' : "before \n").$body.($right ? 'after' : "\n after");

    assertTemplateResult($expected, $source);
    expect(implode('', iterator_to_array(streamTemplate($source))))->toBe($expected);
})->with([false, true], [false, true], [false, true], [false, true]);

test('empty raw content and custom raw body tags preserve their content', function () {
    assertTemplateResult('', '{%- raw -%}{%- endraw -%}');
    assertTemplateResult(" \n ", "{%- raw -%} \n {%- endraw -%}");
    assertTemplateResult(' {{ invalid | }} ', '{%- verbatim -%} {{ invalid | }} {%- endverbatim -%}',
        factory: \Keepsuit\Liquid\EnvironmentFactory::new()->registerTag(VerbatimOutputTag::class));
});

test('output in raw', function () {
    assertTemplateResult('>{{ test }}<', '> {%- raw -%}{{ test }}{%- endraw -%} <');
    assertTemplateResult('> inner  <', '> {%- raw -%} inner {%- endraw %} <');
    assertTemplateResult('> inner <', '> {%- raw -%} inner {%- endraw -%} <');
    assertTemplateResult('{Hello}', '{% raw %}{{% endraw %}Hello{% raw %}}{% endraw %}');
});

test('open tag in raw', function () {
    assertTemplateResult(' Foobar {% invalid ', '{% raw %} Foobar {% invalid {% endraw %}');
    assertTemplateResult(' Foobar invalid %} ', '{% raw %} Foobar invalid %} {% endraw %}');
    assertTemplateResult(' Foobar {{ invalid ', '{% raw %} Foobar {{ invalid {% endraw %}');
    assertTemplateResult(' Foobar invalid }} ', '{% raw %} Foobar invalid }} {% endraw %}');
    assertTemplateResult(' Foobar {% invalid {% {% endraw ', '{% raw %} Foobar {% invalid {% {% endraw {% endraw %}');
    assertTemplateResult(' Foobar {% {% {% ', '{% raw %} Foobar {% {% {% {% endraw %}');
    assertTemplateResult(' test {% raw %} {% endraw %}', '{% raw %} test {% raw %} {% {% endraw %}endraw %}');
    assertTemplateResult(' Foobar {{ invalid 1', '{% raw %} Foobar {{ invalid {% endraw %}{{ 1 }}');
    assertTemplateResult(' Foobar {% foo {% bar %}', '{% raw %} Foobar {% foo {% bar %}{% endraw %}');
});

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
