<?php

test('tag in raw', function () {
    assertTemplateResult(
        '{% comment %} test {% endcomment %}',
        '{% raw %}{% comment %} test {% endcomment %}{% endraw %}',
    );
});

test('output in raw', function () {
    assertTemplateResult('>{{ test }}<', '> {%- raw -%}{{ test }}{%- endraw -%} <');
    assertTemplateResult('> inner  <', '> {%- raw -%} inner {%- endraw %} <');
    assertTemplateResult('> inner <', '> {%- raw -%} inner {%- endraw -%} <');
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
