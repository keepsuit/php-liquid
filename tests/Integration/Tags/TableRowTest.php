<?php

test('table row', function () {
    assertTemplateResult(
        '<tr class="row1"><td class="col1"> 1 </td><td class="col2"> 2 </td><td class="col3"> 3 </td></tr><tr class="row2"><td class="col1"> 4 </td><td class="col2"> 5 </td><td class="col3"> 6 </td></tr>',
        '{% tablerow n in numbers cols:3%} {{n}} {% endtablerow %}',
        ['numbers' => [1, 2, 3, 4, 5, 6]],
    );

    assertTemplateResult(
        '<tr class="row1"></tr>',
        '{% tablerow n in numbers cols:3%} {{n}} {% endtablerow %}',
        ['numbers' => []],
    );
});

test('table row with different cols', function () {
    assertTemplateResult(
        '<tr class="row1"><td class="col1"> 1 </td><td class="col2"> 2 </td><td class="col3"> 3 </td><td class="col4"> 4 </td><td class="col5"> 5 </td></tr><tr class="row2"><td class="col1"> 6 </td></tr>',
        '{% tablerow n in numbers cols:5%} {{n}} {% endtablerow %}',
        ['numbers' => [1, 2, 3, 4, 5, 6]],
    );
});

test('table col counter', function () {
    assertTemplateResult(
        '<tr class="row1"><td class="col1">1</td><td class="col2">2</td></tr><tr class="row2"><td class="col1">1</td><td class="col2">2</td></tr><tr class="row3"><td class="col1">1</td><td class="col2">2</td></tr>',
        '{% tablerow n in numbers cols:2%}{{tablerowloop.col}}{% endtablerow %}',
        ['numbers' => [1, 2, 3, 4, 5, 6]],
    );
});

test('quoted fragment', function () {
    assertTemplateResult(
        '<tr class="row1"><td class="col1"> 1 </td><td class="col2"> 2 </td><td class="col3"> 3 </td></tr><tr class="row2"><td class="col1"> 4 </td><td class="col2"> 5 </td><td class="col3"> 6 </td></tr>',
        '{% tablerow n in collections.frontpage cols:3%} {{n}} {% endtablerow %}',
        ['collections' => ['frontpage' => [1, 2, 3, 4, 5, 6]]],
    );
    assertTemplateResult(
        '<tr class="row1"><td class="col1"> 1 </td><td class="col2"> 2 </td><td class="col3"> 3 </td></tr><tr class="row2"><td class="col1"> 4 </td><td class="col2"> 5 </td><td class="col3"> 6 </td></tr>',
        "{% tablerow n in collections['frontpage'] cols:3%} {{n}} {% endtablerow %}",
        ['collections' => ['frontpage' => [1, 2, 3, 4, 5, 6]]],
    );
});

test('enumerable drop', function () {
    assertTemplateResult(
        '<tr class="row1"><td class="col1"> 1 </td><td class="col2"> 2 </td><td class="col3"> 3 </td></tr><tr class="row2"><td class="col1"> 4 </td><td class="col2"> 5 </td><td class="col3"> 6 </td></tr>',
        '{% tablerow n in numbers cols:3%} {{n}} {% endtablerow %}',
        ['numbers' => new \Keepsuit\Liquid\Tests\Stubs\IteratorDrop([1, 2, 3, 4, 5, 6])],
    );
});

test('offset and limit', function () {
    assertTemplateResult(
        '<tr class="row1"><td class="col1"> 1 </td><td class="col2"> 2 </td><td class="col3"> 3 </td></tr><tr class="row2"><td class="col1"> 4 </td><td class="col2"> 5 </td><td class="col3"> 6 </td></tr>',
        '{% tablerow n in numbers cols:3 offset:1 limit:6%} {{n}} {% endtablerow %}',
        ['numbers' => [0, 1, 2, 3, 4, 5, 6, 7]],
    );
});

test('blank string not iterable', function () {
    assertTemplateResult(
        '<tr class="row1"></tr>',
        '{% tablerow char in characters cols:3 %}I WILL NOT BE OUTPUT{% endtablerow %}',
        ['characters' => ''],
    );
});

test('cols null constant same as evaluated null expression', function () {
    $expect = '<tr class="row1"><td class="col1">false</td><td class="col2">false</td></tr>';

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
    $expect = '<tr class="row1"></tr>';

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
    $expect = '<tr class="row1"><td class="col1">1:false</td><td class="col2">2:true</td></tr>';

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
    <tr class="row1"><td class="col1">
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
    HTML;

    assertTemplateResult($expect, $template);
});

test('tablerow renders correct error message for invalid parameters', function () {
    assertTemplateResult(
        'Liquid error (line 1): invalid integer',
        '{% tablerow n in (1...10) limit:true %} {{n}} {% endtablerow %}',
        renderErrors: true,
    );
    assertTemplateResult(
        'Liquid error (line 1): invalid integer',
        '{% tablerow n in (1...10) offset:true %} {{n}} {% endtablerow %}',
        renderErrors: true,
    );
    assertTemplateResult(
        'Liquid error (line 1): invalid integer',
        '{% tablerow n in (1...10) cols:true %} {{n}} {% endtablerow %}',
        renderErrors: true,
    );
});
