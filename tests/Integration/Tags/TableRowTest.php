<?php

test('tablerow strict parsing rejects malformed params', function () {
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Expected :, got Number - Valid syntax: tablerow <var> in <collection> [attributes...]',
        '{% tablerow n in numbers cols 3 %}{% endtablerow %}',
        ['numbers' => [1, 2, 3]]);
    assertMatchSyntaxError(
        'Liquid syntax error (line 1): Unexpected end of template - Valid syntax: tablerow <var> in <collection> [attributes...]',
        '{% tablerow n in numbers cols: 3, limit %}{% endtablerow %}',
        ['numbers' => [1, 2, 3]]);
});

describe('rendering with template backends', function () {
    test('tablerow selects limited ranges before materialization', function (bool $compiled) {
        $source = '{% tablerow i in (1..1000000000) offset:-2 limit:1 cols:1 %}{{ i }}{% endtablerow %}';
        $expected = "<tr class=\"row1\">\n<td class=\"col1\">999999999</td></tr>\n";

        assertTemplateResult($expected, $source, compiled: $compiled);
        expect(implode('', iterator_to_array(streamTemplate($source, compiled: $compiled))))->toBe($expected);
    });

    test('tablerow markup matches Shopify byte for byte in render and stream', function (bool $compiled) {
        $source = '{% tablerow i in arr cols:2 %}{{ i }}{% endtablerow %}';
        $expected = "<tr class=\"row1\">\n<td class=\"col1\">1</td><td class=\"col2\">2</td></tr>\n<tr class=\"row2\"><td class=\"col1\">3</td></tr>\n";

        assertTemplateResult($expected, $source, data: ['arr' => [1, 2, 3]], compiled: $compiled);
        expect(implode('', iterator_to_array(streamTemplate($source, data: ['arr' => [1, 2, 3]], compiled: $compiled))))->toBe($expected);
    });

    test('tablerow over nil renders nothing', function (bool $compiled) {
        assertTemplateResult('', '{% tablerow i in nil %}x{% endtablerow %}', compiled: $compiled);
        assertTemplateResult('', '{% tablerow i in items %}x{% endtablerow %}', data: ['items' => null], strictVariables: true, compiled: $compiled);
        expect(implode('', iterator_to_array(streamTemplate('{% tablerow i in nil %}x{% endtablerow %}', compiled: $compiled))))->toBe('');
    });

    test('tablerow over a string renders one cell and over a number renders an empty row', function (bool $compiled) {
        assertTemplateResult("<tr class=\"row1\">\n<td class=\"col1\">ab</td></tr>\n", '{% tablerow i in s %}{{ i }}{% endtablerow %}', data: ['s' => 'ab'], compiled: $compiled);
        assertTemplateResult("<tr class=\"row1\">\n</tr>\n", '{% tablerow i in n %}{{ i }}{% endtablerow %}', data: ['n' => 5], compiled: $compiled);
        assertTemplateResult('', '{% tablerow i in f %}x{% endtablerow %}', data: ['f' => false], compiled: $compiled);
    });

    test('tablerow reports missing variables in strict variables mode', function (bool $compiled) {
        expect(fn () => renderTemplate('{% tablerow i in missing %}x{% endtablerow %}', strictVariables: true, compiled: $compiled))
            ->toThrow(\Keepsuit\Liquid\Exceptions\UndefinedVariableException::class);
    });

    test('table row', function (bool $compiled) {
        assertTemplateResult(
            <<<'HTML'
        <tr class="row1">
        <td class="col1"> 1 </td><td class="col2"> 2 </td><td class="col3"> 3 </td></tr>
        <tr class="row2"><td class="col1"> 4 </td><td class="col2"> 5 </td><td class="col3"> 6 </td></tr>
        HTML."\n",
            '{% tablerow n in numbers cols:3%} {{n}} {% endtablerow %}',
            ['numbers' => [1, 2, 3, 4, 5, 6]],
            compiled: $compiled);

        assertTemplateResult(
            "<tr class=\"row1\">\n</tr>\n",
            '{% tablerow n in numbers cols:3%} {{n}} {% endtablerow %}',
            ['numbers' => []],
            compiled: $compiled);
    });

    test('table row with different cols', function (bool $compiled) {
        assertTemplateResult(
            <<<'HTML'
        <tr class="row1">
        <td class="col1"> 1 </td><td class="col2"> 2 </td><td class="col3"> 3 </td><td class="col4"> 4 </td><td class="col5"> 5 </td></tr>
        <tr class="row2"><td class="col1"> 6 </td></tr>
        HTML."\n",
            '{% tablerow n in numbers cols:5%} {{n}} {% endtablerow %}',
            ['numbers' => [1, 2, 3, 4, 5, 6]],
            compiled: $compiled);
    });

    test('table col counter', function (bool $compiled) {
        assertTemplateResult(
            <<<'HTML'
        <tr class="row1">
        <td class="col1">1</td><td class="col2">2</td></tr>
        <tr class="row2"><td class="col1">1</td><td class="col2">2</td></tr>
        <tr class="row3"><td class="col1">1</td><td class="col2">2</td></tr>
        HTML."\n",
            '{% tablerow n in numbers cols:2%}{{tablerowloop.col}}{% endtablerow %}',
            ['numbers' => [1, 2, 3, 4, 5, 6]],
            compiled: $compiled);
    });

    test('quoted fragment', function (bool $compiled) {
        assertTemplateResult(
            <<<'HTML'
        <tr class="row1">
        <td class="col1"> 1 </td><td class="col2"> 2 </td><td class="col3"> 3 </td></tr>
        <tr class="row2"><td class="col1"> 4 </td><td class="col2"> 5 </td><td class="col3"> 6 </td></tr>
        HTML."\n",
            '{% tablerow n in collections.frontpage cols:3%} {{n}} {% endtablerow %}',
            ['collections' => ['frontpage' => [1, 2, 3, 4, 5, 6]]],
            compiled: $compiled);
        assertTemplateResult(
            <<<'HTML'
        <tr class="row1">
        <td class="col1"> 1 </td><td class="col2"> 2 </td><td class="col3"> 3 </td></tr>
        <tr class="row2"><td class="col1"> 4 </td><td class="col2"> 5 </td><td class="col3"> 6 </td></tr>
        HTML."\n",
            "{% tablerow n in collections['frontpage'] cols:3%} {{n}} {% endtablerow %}",
            ['collections' => ['frontpage' => [1, 2, 3, 4, 5, 6]]],
            compiled: $compiled);
    });

    test('enumerable drop', function (bool $compiled) {
        assertTemplateResult(
            <<<'HTML'
        <tr class="row1">
        <td class="col1"> 1 </td><td class="col2"> 2 </td><td class="col3"> 3 </td></tr>
        <tr class="row2"><td class="col1"> 4 </td><td class="col2"> 5 </td><td class="col3"> 6 </td></tr>
        HTML."\n",
            '{% tablerow n in numbers cols:3%} {{n}} {% endtablerow %}',
            ['numbers' => new \Keepsuit\Liquid\Tests\Stubs\IteratorDrop([1, 2, 3, 4, 5, 6])],
            compiled: $compiled);
    });

    test('offset and limit', function (bool $compiled) {
        assertTemplateResult(
            <<<'HTML'
        <tr class="row1">
        <td class="col1"> 1 </td><td class="col2"> 2 </td><td class="col3"> 3 </td></tr>
        <tr class="row2"><td class="col1"> 4 </td><td class="col2"> 5 </td><td class="col3"> 6 </td></tr>
        HTML."\n",
            '{% tablerow n in numbers cols:3 offset:1 limit:6%} {{n}} {% endtablerow %}',
            ['numbers' => [0, 1, 2, 3, 4, 5, 6, 7]],
            compiled: $compiled);

        assertTemplateResult(
            <<<'HTML'
        <tr class="row1">
        <td class="col1"> 1 </td><td class="col2"> 2 </td><td class="col3"> 3 </td></tr>
        <tr class="row2"><td class="col1"> 4 </td><td class="col2"> 5 </td><td class="col3"> 6 </td></tr>
        HTML."\n",
            '{% tablerow n in numbers, cols:3, offset:1, limit:6 %} {{n}} {% endtablerow %}',
            ['numbers' => [0, 1, 2, 3, 4, 5, 6, 7]],
            compiled: $compiled);
    });

    test('blank string not iterable', function (bool $compiled) {
        assertTemplateResult(
            "<tr class=\"row1\">\n</tr>\n",
            '{% tablerow char in characters cols:3 %}I WILL NOT BE OUTPUT{% endtablerow %}',
            ['characters' => ''],
            compiled: $compiled);
    });

    test('cols null constant same as evaluated null expression', function (bool $compiled) {
        $expect = <<<'HTML'
        <tr class="row1">
        <td class="col1">false</td><td class="col2">false</td></tr>
        HTML."\n";

        assertTemplateResult(
            $expect,
            '{% tablerow i in (1..2) cols:nil %}{{ tablerowloop.col_last }}{% endtablerow %}',
            compiled: $compiled);
        assertTemplateResult(
            $expect,
            '{% tablerow i in (1..2) cols:var %}{{ tablerowloop.col_last }}{% endtablerow %}',
            ['var' => null],
            compiled: $compiled);
    });

    test('nil limit is treated as zero', function (bool $compiled) {
        $expect = "<tr class=\"row1\">\n</tr>\n";

        assertTemplateResult(
            $expect,
            '{% tablerow i in (1..2) limit:nil %}{{ i }}{% endtablerow %}', compiled: $compiled);
        assertTemplateResult(
            $expect,
            '{% tablerow i in (1..2) limit:var %}{{ i }}{% endtablerow %}',
            ['var' => null],
            compiled: $compiled);
    });

    test('nil offset is treated as zero', function (bool $compiled) {
        $expect = <<<'HTML'
        <tr class="row1">
        <td class="col1">1:false</td><td class="col2">2:true</td></tr>
        HTML."\n";

        assertTemplateResult(
            $expect,
            '{% tablerow i in (1..2) offset:nil %}{{ i }}:{{ tablerowloop.col_last }}{% endtablerow %}',
            compiled: $compiled);
        assertTemplateResult(
            $expect,
            '{% tablerow i in (1..2) offset:var %}{{ i }}:{{ tablerowloop.col_last }}{% endtablerow %}',
            ['var' => null],
            compiled: $compiled);
    });

    test('tablerow loop drop attributes', function (bool $compiled) {
        $template = <<<'LIQUID'
    {% tablerow i in (1..2) %}
    col: {{ tablerowloop.col }}
    col0: {{ tablerowloop.col0 }}
    col_first: {{ tablerowloop.col_first }}
    col_last: {{ tablerowloop.col_last }}
    first: {{ tablerowloop.first }}
    index: {{ tablerowloop.index }}
    index0: {{ tablerowloop.index0 }}
    last: {{ tablerowloop.last }}
    length: {{ tablerowloop.length }}
    rindex: {{ tablerowloop.rindex }}
    rindex0: {{ tablerowloop.rindex0 }}
    row: {{ tablerowloop.row }}
    {% endtablerow %}
    LIQUID;

        $expect = <<<'HTML'
    <tr class="row1">
    <td class="col1">
    col: 1
    col0: 0
    col_first: true
    col_last: false
    first: true
    index: 1
    index0: 0
    last: false
    length: 2
    rindex: 2
    rindex0: 1
    row: 1
    </td><td class="col2">
    col: 2
    col0: 1
    col_first: false
    col_last: true
    first: false
    index: 2
    index0: 1
    last: true
    length: 2
    rindex: 1
    rindex0: 0
    row: 1
    </td></tr>
    HTML."\n";

        assertTemplateResult($expect, $template, compiled: $compiled);
    });

    test('tablerow renders correct error message for invalid parameters', function (bool $compiled) {
        assertTemplateResult(
            'Liquid error (line 1): invalid integer',
            '{% tablerow n in (1..10) limit:true %} {{n}} {% endtablerow %}',
            renderErrors: true,
            compiled: $compiled);
        assertTemplateResult(
            'Liquid error (line 1): invalid integer',
            '{% tablerow n in (1..10) offset:true %} {{n}} {% endtablerow %}',
            renderErrors: true,
            compiled: $compiled);
        assertTemplateResult(
            'Liquid error (line 1): invalid integer',
            '{% tablerow n in (1..10) cols:true %} {{n}} {% endtablerow %}',
            renderErrors: true,
            compiled: $compiled);
    });

    test('tablerow handles interrupts', function (bool $compiled) {
        assertTemplateResult(
            "<tr class=\"row1\">\n<td class=\"col1\"> 1 </td></tr>\n",
            '{% tablerow n in (1..3) cols:2 %} {{n}} {% break %} {{n}} {% endtablerow %}', compiled: $compiled);

        assertTemplateResult(
            "<tr class=\"row1\">\n<td class=\"col1\"> 1 </td><td class=\"col2\"> 2 </td></tr>\n<tr class=\"row2\"><td class=\"col1\"> 3 </td></tr>\n",
            '{% tablerow n in (1..3) cols:2 %} {{n}} {% continue %} {{n}} {% endtablerow %}',
            compiled: $compiled);
    });

    test('tablerow does not leak interrupts', function (bool $compiled) {
        $template = <<<'LIQUID'
        {% for i in (1..2) -%}
        {% for j in (1..2) -%}
        {% tablerow k in (1..3) %}{% break %}{% endtablerow %}
        loop j={{ j }}
        {% endfor -%}
        loop i={{ i }}
        {% endfor -%}
        after loop
        LIQUID;

        $expected = <<<'HTML'
        <tr class="row1">
        <td class="col1"></td></tr>

        loop j=1
        <tr class="row1">
        <td class="col1"></td></tr>

        loop j=2
        loop i=1
        <tr class="row1">
        <td class="col1"></td></tr>

        loop j=1
        <tr class="row1">
        <td class="col1"></td></tr>

        loop j=2
        loop i=2
        after loop
        HTML;

        assertTemplateResult($expected, $template, compiled: $compiled);
    });
})->with('template backends');
