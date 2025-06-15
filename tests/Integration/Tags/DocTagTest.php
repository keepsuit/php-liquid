<?php

test('doc tag', function () {
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

    assertTemplateResult('', $template);
});

test('doc tag does not support extra arguments', function () {
    $template = <<<'LIQUID'
    {% doc extra %}
    {% enddoc %}
    LIQUID;

    assertMatchSyntaxError('Liquid syntax error (line 2): Unexpected token Identifier: "extra"', $template);
});

test('doc tag must support valid tags', function () {
    assertMatchSyntaxError("Liquid syntax error (line 1): 'doc' tag was never closed", '{% doc %} foo');
    assertMatchSyntaxError('Liquid syntax error (line 1): Unexpected character }', '{% doc } foo {% enddoc %}');
    assertMatchSyntaxError('Liquid syntax error (line 1): Unexpected character }', '{% doc } foo %}{% enddoc %}');
});

test('doc tag ignores liquid nodes', function () {
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

    assertTemplateResult('', $template);
});

test('doc tag ignores unclosed liquid tags', function () {
    $template = <<<'LIQUID'
    {% doc %}
        {% if true %}
    {% enddoc %}
    LIQUID;

    assertTemplateResult('', $template);
});

test('doc tag does not allow nested docs', function () {
    $template = <<<'LIQUID'
    {% doc %}
        {% doc %}
            {% doc %}
    {% enddoc %}
    LIQUID;

    assertMatchSyntaxError('Liquid syntax error (line 4): Nested doc tags are not allowed', $template);
});

test('doc tag ignores nested raw tags', function () {
    $template = <<<'LIQUID'
    {% doc %}
        {% raw %}
    {% enddoc %}
    LIQUID;

    assertTemplateResult('', $template);
});

test('doc tag ignores unclosed assign', function () {
    $template = <<<'LIQUID'
    {% doc %}
        {% assign foo = "1"
    {% enddoc %}
    LIQUID;

    assertTemplateResult('', $template);
});

test('doc tag ignores malformed syntax', function () {
    $template = <<<'LIQUID'
    {% doc %}
        {% {{
    {%- enddoc %}
    LIQUID;

    assertTemplateResult('', $template);
});

test('doc tag preserves error line numbers', function () {
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
        renderErrors: true
    );
});

test('doc tag whitespace control', function () {
    assertTemplateResult('Hello!', '      {%- doc -%}123{%- enddoc -%}Hello!');
    assertTemplateResult('Hello!', '{%- doc -%}123{%- enddoc -%}     Hello!');
    assertTemplateResult('Hello!', '      {%- doc -%}123{%- enddoc -%}     Hello!');
    assertTemplateResult('Hello!', <<<'LIQUID'
      {%- doc %}Whitespace control!{% enddoc -%}
      Hello!
    LIQUID);
});

test('doc tag delimiter handling', function () {
    assertTemplateResult('', <<<'LIQUID'
    {% if true -%}
        {% doc %}
            {% docEXTRA %}wut{% enddocEXTRA %}xyz
        {% enddoc %}
    {%- endif %}
    LIQUID);
    assertMatchSyntaxError("Liquid syntax error (line 1): 'doc' tag was never closed", '{% doc %}123{% enddoc xyz %}');
    assertTemplateResult('', "{% doc %}123{% enddoc\n   xyz %}{% enddoc %}");
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

    $template = parseTemplate($template);
    $docTag = $template->root->body->children()[0] ?? null;

    expect($docTag)
        ->toBeInstanceOf(\Keepsuit\Liquid\Tags\DocTag::class)
        ->getBody()->toBeInstanceOf(\Keepsuit\Liquid\Nodes\Raw::class)
        ->getBody()->value->toBe($content);
});
