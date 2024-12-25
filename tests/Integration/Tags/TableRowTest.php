<?php

test('table row', function () {
    assertTemplateResult(
        <<<'HTML'
        <tr class="row1">
        <td class="col1"> 1 </td>
        <td class="col2"> 2 </td>
        <td class="col3"> 3 </td>
        </tr>
        <tr class="row2">
        <td class="col1"> 4 </td>
        <td class="col2"> 5 </td>
        <td class="col3"> 6 </td>
        </tr>
        HTML,
        '{% tablerow n in numbers cols:3%} {{n}} {% endtablerow %}',
        ['numbers' => [1, 2, 3, 4, 5, 6]],
    );

    assertTemplateResult(
        "<tr class=\"row1\">\n</tr>",
        '{% tablerow n in numbers cols:3%} {{n}} {% endtablerow %}',
        ['numbers' => []],
    );
});

test('table row with different cols', function () {
    assertTemplateResult(
        <<<'HTML'
        <tr class="row1">
        <td class="col1"> 1 </td>
        <td class="col2"> 2 </td>
        <td class="col3"> 3 </td>
        <td class="col4"> 4 </td>
        <td class="col5"> 5 </td>
        </tr>
        <tr class="row2">
        <td class="col1"> 6 </td>
        </tr>
        HTML,
        '{% tablerow n in numbers cols:5%} {{n}} {% endtablerow %}',
        ['numbers' => [1, 2, 3, 4, 5, 6]],
    );
});

test('table col counter', function () {
    assertTemplateResult(
        <<<'HTML'
        <tr class="row1">
        <td class="col1">1</td>
        <td class="col2">2</td>
        </tr>
        <tr class="row2">
        <td class="col1">1</td>
        <td class="col2">2</td>
        </tr>
        <tr class="row3">
        <td class="col1">1</td>
        <td class="col2">2</td>
        </tr>
        HTML,
        '{% tablerow n in numbers cols:2%}{{tablerowloop.col}}{% endtablerow %}',
        ['numbers' => [1, 2, 3, 4, 5, 6]],
    );
});

test('quoted fragment', function () {
    assertTemplateResult(
        <<<'HTML'
        <tr class="row1">
        <td class="col1"> 1 </td>
        <td class="col2"> 2 </td>
        <td class="col3"> 3 </td>
        </tr>
        <tr class="row2">
        <td class="col1"> 4 </td>
        <td class="col2"> 5 </td>
        <td class="col3"> 6 </td>
        </tr>
        HTML,
        '{% tablerow n in collections.frontpage cols:3%} {{n}} {% endtablerow %}',
        ['collections' => ['frontpage' => [1, 2, 3, 4, 5, 6]]],
    );
    assertTemplateResult(
        <<<'HTML'
        <tr class="row1">
        <td class="col1"> 1 </td>
        <td class="col2"> 2 </td>
        <td class="col3"> 3 </td>
        </tr>
        <tr class="row2">
        <td class="col1"> 4 </td>
        <td class="col2"> 5 </td>
        <td class="col3"> 6 </td>
        </tr>
        HTML,
        "{% tablerow n in collections['frontpage'] cols:3%} {{n}} {% endtablerow %}",
        ['collections' => ['frontpage' => [1, 2, 3, 4, 5, 6]]],
    );
});

test('enumerable drop', function () {
    assertTemplateResult(
        <<<'HTML'
        <tr class="row1">
        <td class="col1"> 1 </td>
        <td class="col2"> 2 </td>
        <td class="col3"> 3 </td>
        </tr>
        <tr class="row2">
        <td class="col1"> 4 </td>
        <td class="col2"> 5 </td>
        <td class="col3"> 6 </td>
        </tr>
        HTML,
        '{% tablerow n in numbers cols:3%} {{n}} {% endtablerow %}',
        ['numbers' => new \Keepsuit\Liquid\Tests\Stubs\IteratorDrop([1, 2, 3, 4, 5, 6])],
    );
});

test('offset and limit', function () {
    assertTemplateResult(
        <<<'HTML'
        <tr class="row1">
        <td class="col1"> 1 </td>
        <td class="col2"> 2 </td>
        <td class="col3"> 3 </td>
        </tr>
        <tr class="row2">
        <td class="col1"> 4 </td>
        <td class="col2"> 5 </td>
        <td class="col3"> 6 </td>
        </tr>
        HTML,
        '{% tablerow n in numbers cols:3 offset:1 limit:6%} {{n}} {% endtablerow %}',
        ['numbers' => [0, 1, 2, 3, 4, 5, 6, 7]],
    );
});

test('blank string not iterable', function () {
    assertTemplateResult(
        "<tr class=\"row1\">\n</tr>",
        '{% tablerow char in characters cols:3 %}I WILL NOT BE OUTPUT{% endtablerow %}',
        ['characters' => ''],
    );
});

test('cols null constant same as evaluated null expression', function () {
    $expect = <<<'HTML'
        <tr class="row1">
        <td class="col1">false</td>
        <td class="col2">false</td>
        </tr>
        HTML;

    assertTemplateResult(
        $expect,
        '{% tablerow i in (1..2) cols:nil %}{{ tablerowloop.col_last }}{% endtablerow %}',
    );
    assertTemplateResult(
        $expect,
        '{% tablerow i in (1..2) cols:var %}{{ tablerowloop.col_last }}{% endtablerow %}',
        ['var' => null],
    );
});

test('nil limit is treated as zero', function () {
    $expect = "<tr class=\"row1\">\n</tr>";

    assertTemplateResult(
        $expect,
        '{% tablerow i in (1..2) limit:nil %}{{ i }}{% endtablerow %}'
    );
    assertTemplateResult(
        $expect,
        '{% tablerow i in (1..2) limit:var %}{{ i }}{% endtablerow %}',
        ['var' => null],
    );
});

test('nil offset is treated as zero', function () {
    $expect = <<<'HTML'
        <tr class="row1">
        <td class="col1">1:false</td>
        <td class="col2">2:true</td>
        </tr>
        HTML;

    assertTemplateResult(
        $expect,
        '{% tablerow i in (1..2) offset:nil %}{{ i }}:{{ tablerowloop.col_last }}{% endtablerow %}',
    );
    assertTemplateResult(
        $expect,
        '{% tablerow i in (1..2) offset:var %}{{ i }}:{{ tablerowloop.col_last }}{% endtablerow %}',
        ['var' => null],
    );
});

test('tablerow loop drop attributes', function () {
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
    </td>
    <td class="col2">
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
    </td>
    </tr>
    HTML;

    assertTemplateResult($expect, $template);
});

test('tablerow renders correct error message for invalid parameters', function () {
    assertTemplateResult(
        'Liquid error (line 1): invalid integer',
        '{% tablerow n in (1..10) limit:true %} {{n}} {% endtablerow %}',
        renderErrors: true,
    );
    assertTemplateResult(
        'Liquid error (line 1): invalid integer',
        '{% tablerow n in (1..10) offset:true %} {{n}} {% endtablerow %}',
        renderErrors: true,
    );
    assertTemplateResult(
        'Liquid error (line 1): invalid integer',
        '{% tablerow n in (1..10) cols:true %} {{n}} {% endtablerow %}',
        renderErrors: true,
    );
});

test('tablerow handles interrupts', function () {
    assertTemplateResult(
        "<tr class=\"row1\">\n<td class=\"col1\"> 1 </td>\n</tr>",
        '{% tablerow n in (1..3) cols:2 %} {{n}} {% break %} {{n}} {% endtablerow %}'
    );

    assertTemplateResult(
        "<tr class=\"row1\">\n<td class=\"col1\"> 1 </td>\n<td class=\"col2\"> 2 </td>\n</tr>\n<tr class=\"row2\">\n<td class=\"col1\"> 3 </td>\n</tr>",
        '{% tablerow n in (1..3) cols:2 %} {{n}} {% continue %} {{n}} {% endtablerow %}',
    );
});

test('tablerow does not leak interrupts', function () {
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
        <td class="col1"></td>
        </tr>
        loop j=1
        <tr class="row1">
        <td class="col1"></td>
        </tr>
        loop j=2
        loop i=1
        <tr class="row1">
        <td class="col1"></td>
        </tr>
        loop j=1
        <tr class="row1">
        <td class="col1"></td>
        </tr>
        loop j=2
        loop i=2
        after loop
        HTML;

    assertTemplateResult($expected, $template);
});
