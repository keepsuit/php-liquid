<?php

test('for', function () {
    assertTemplateResult(
        ' yo  yo  yo  yo ',
        '{%for item in array%} yo {%endfor%}',
        assigns: ['array' => [1, 2, 3, 4]]
    );
    assertTemplateResult(
        'yoyo',
        '{%for item in array%}yo{%endfor%}',
        assigns: ['array' => [1, 2]]
    );
    assertTemplateResult(
        ' yo ',
        '{%for item in array%} yo {%endfor%}',
        assigns: ['array' => [1]]
    );
    assertTemplateResult(
        '',
        '{%for item in array%}{%endfor%}',
        assigns: ['array' => [1, 2]]
    );
    assertTemplateResult(
        <<<'LIQUID'

          yo

          yo

          yo

        LIQUID,
        <<<'LIQUID'
        {%for item in array%}
          yo
        {%endfor%}
        LIQUID,
        assigns: ['array' => [1, 2, 3]]
    );
});

test('for reversed', function () {
    assertTemplateResult(
        '321',
        '{%for item in array reversed %}{{item}}{%endfor%}',
        assigns: ['array' => [1, 2, 3]]
    );
});

test('for with range', function () {
    assertTemplateResult(
        ' 1  2  3 ',
        '{%for item in (1..3) %} {{item}} {%endfor%}'
    );

    expect(fn () => parseTemplate('{% for i in (a..2) %}{% endfor %}', assigns: ['a' => [1, 2]]))->toThrow('Invalid integer');

    assertTemplateResult(
        ' 0  1  2  3 ',
        '{% for item in (a..3) %} {{item}} {% endfor %}',
        assigns: ['a' => 'invalid integer']
    );
});

test('for with variable range', function () {
    assertTemplateResult(
        ' 1  2  3 ',
        '{%for item in (1..foobar) %} {{item}} {%endfor%}',
        assigns: ['foobar' => 3]
    );
    assertTemplateResult(
        ' 1  2  3 ',
        '{%for item in (1..foobar.value) %} {{item}} {%endfor%}',
        assigns: ['foobar' => ['value' => 3]]
    );
    assertTemplateResult(
        ' 1  2  3 ',
        '{%for item in (1..foobar.value) %} {{item}} {%endfor%}',
        assigns: ['foobar' => new \Keepsuit\Liquid\Tests\Stubs\ThingWithValue(3)]
    );
});

test('for with variable', function () {
    assertTemplateResult(
        ' 1  2  3 ',
        '{%for item in array%} {{item}} {%endfor%}',
        assigns: ['array' => [1, 2, 3]]
    );
    assertTemplateResult(
        '123',
        '{%for item in array%}{{item}}{%endfor%}',
        assigns: ['array' => [1, 2, 3]]
    );
    assertTemplateResult(
        '123',
        '{% for item in array %}{{item}}{% endfor %}',
        assigns: ['array' => [1, 2, 3]]
    );

    assertTemplateResult(
        'abcd',
        '{%for item in array%}{{item}}{%endfor%}',
        assigns: ['array' => ['a', 'b', 'c', 'd']]
    );
    assertTemplateResult(
        'a b c',
        '{%for item in array%}{{item}}{%endfor%}',
        assigns: ['array' => ['a', ' ', 'b', ' ', 'c']]
    );
    assertTemplateResult(
        'abc',
        '{%for item in array%}{{item}}{%endfor%}',
        assigns: ['array' => ['a', '', 'b', '', 'c']]
    );
});

test('for helpers', function () {
    $assigns = ['array' => [1, 2, 3]];

    assertTemplateResult(' 1/3  2/3  3/3 ', '{%for item in array%} {{forloop.index}}/{{forloop.length}} {%endfor%}', assigns: $assigns);
    assertTemplateResult(' 1  2  3 ', '{%for item in array%} {{forloop.index}} {%endfor%}', assigns: $assigns);
    assertTemplateResult(' 0  1  2 ', '{%for item in array%} {{forloop.index0}} {%endfor%}', assigns: $assigns);
    assertTemplateResult(' 2  1  0 ', '{%for item in array%} {{forloop.rindex0}} {%endfor%}', assigns: $assigns);
    assertTemplateResult(' 3  2  1 ', '{%for item in array%} {{forloop.rindex}} {%endfor%}', assigns: $assigns);
    assertTemplateResult(' true  false  false ', '{%for item in array%} {{forloop.first}} {%endfor%}', assigns: $assigns);
    assertTemplateResult(' false  false  true ', '{%for item in array%} {{forloop.last}} {%endfor%}', assigns: $assigns);
});
