<?php

test('doc tag does not support extra arguments', function () {
    $template = <<<'LIQUID'
    {% doc extra %}
    {% enddoc %}
    LIQUID;

    assertMatchSyntaxError('Liquid syntax error (line 2): Unexpected token Identifier: "extra" - Valid syntax: doc', $template);
});

test('doc tag must support valid tags', function () {
    assertMatchSyntaxError("Liquid syntax error (line 1): 'doc' tag was never closed", '{% doc %} foo');
    assertMatchSyntaxError('Liquid syntax error (line 1): Unexpected character }', '{% doc } foo {% enddoc %}');
    assertMatchSyntaxError('Liquid syntax error (line 1): Unexpected character }', '{% doc } foo %}{% enddoc %}');
});

test('doc tag does not allow nested docs', function () {
    $template = <<<'LIQUID'
    {% doc %}
        {% doc %}
            {% doc %}
    {% enddoc %}
    LIQUID;

    assertMatchSyntaxError('Liquid syntax error (line 4): Nested doc tags are not allowed - Valid syntax: doc', $template);
});

test('access doc tag body', function () {
    $content = <<<'EOF'
    Renders loading-spinner.
    @param {string} foo - some foo
    @param {string} [bar] - optional bar
    EOF;

    $template = <<<LIQUID
    {% doc %}$content{% enddoc %}
    LIQUID;

    $template = parseSource($template);
    $docTag = $template->root->body->children()[0] ?? null;

    expect($docTag)
        ->toBeInstanceOf(\Keepsuit\Liquid\Tags\DocTag::class)
        ->getBody()->toBeInstanceOf(\Keepsuit\Liquid\Nodes\Raw::class)
        ->getBody()->value->toBe($content);
});

describe('rendering with template backends', function () {
    test('doc tag', function (bool $compiled) {
        $template = <<<'LIQUID'
    {% doc %}
        Renders loading-spinner.
        @param {string} foo - some foo
        @param {string} [bar] - optional bar
        @example
        {% render 'loading-spinner', foo: 'foo' %}
        {% render 'loading-spinner', foo: 'foo', bar: 'bar' %}
    {% enddoc %}
    LIQUID;

        assertTemplateResult('', $template, compiled: $compiled);
    });

    test('doc tag ignores liquid nodes', function (bool $compiled) {
        $template = <<<'LIQUID'
    {% doc %}
        {% if true %}
        {% if ... %}
        {%- for ? -%}
        {% while true %}
        {%
            unless if
        %}
        {% endcase %}
    {% enddoc %}
    LIQUID;

        assertTemplateResult('', $template, compiled: $compiled);
    });

    test('doc tag ignores unclosed liquid tags', function (bool $compiled) {
        $template = <<<'LIQUID'
    {% doc %}
        {% if true %}
    {% enddoc %}
    LIQUID;

        assertTemplateResult('', $template, compiled: $compiled);
    });

    test('doc tag ignores nested raw tags', function (bool $compiled) {
        $template = <<<'LIQUID'
    {% doc %}
        {% raw %}
    {% enddoc %}
    LIQUID;

        assertTemplateResult('', $template, compiled: $compiled);
    });

    test('doc tag ignores unclosed assign', function (bool $compiled) {
        $template = <<<'LIQUID'
    {% doc %}
        {% assign foo = "1"
    {% enddoc %}
    LIQUID;

        assertTemplateResult('', $template, compiled: $compiled);
    });

    test('doc tag ignores malformed syntax', function (bool $compiled) {
        $template = <<<'LIQUID'
    {% doc %}
        {% {{
    {%- enddoc %}
    LIQUID;

        assertTemplateResult('', $template, compiled: $compiled);
    });

    test('doc tag preserves error line numbers', function (bool $compiled) {
        $template = <<<'LIQUID'
    {% doc %}
        {% if true %}
    {% enddoc %}
    {{ errors.standard_error }}
    LIQUID;

        $expected = <<<'TEXT'

    Liquid error (line 4): Standard error
    TEXT;

        assertTemplateResult(
            $expected,
            $template,
            ['errors' => new \Keepsuit\Liquid\Tests\Stubs\ErrorDrop],
            renderErrors: true, compiled: $compiled);
    });

    test('doc tag whitespace control', function (bool $compiled) {
        assertTemplateResult('Hello!', '      {%- doc -%}123{%- enddoc -%}Hello!', compiled: $compiled);
        assertTemplateResult('Hello!', '{%- doc -%}123{%- enddoc -%}     Hello!', compiled: $compiled);
        assertTemplateResult('Hello!', '      {%- doc -%}123{%- enddoc -%}     Hello!', compiled: $compiled);
        assertTemplateResult('Hello!', <<<'LIQUID'
      {%- doc %}Whitespace control!{% enddoc -%}
      Hello!
    LIQUID, compiled: $compiled);
    });

    test('doc tag delimiter handling', function (bool $compiled) {
        assertTemplateResult('', <<<'LIQUID'
    {% if true -%}
        {% doc %}
            {% docEXTRA %}wut{% enddocEXTRA %}xyz
        {% enddoc %}
    {%- endif %}
    LIQUID, compiled: $compiled);
        assertMatchSyntaxError("Liquid syntax error (line 1): 'doc' tag was never closed", '{% doc %}123{% enddoc xyz %}', compiled: $compiled);
        assertTemplateResult('', "{% doc %}123{% enddoc\n   xyz %}{% enddoc %}", compiled: $compiled);
    });
})->with('template backends');
