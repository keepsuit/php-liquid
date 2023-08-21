<?php

use Keepsuit\Liquid\Context;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Template;
use Keepsuit\Liquid\Tests\Stubs\ErrorDrop;
use Keepsuit\Liquid\Tests\Stubs\LoaderDrop;
use Keepsuit\Liquid\Tests\Stubs\ThingWithValue;

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

    expect(fn () => renderTemplate('{% for i in (a..2) %}{% endfor %}', assigns: ['a' => [1, 2]]))->toThrow('Invalid integer');

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
        assigns: ['foobar' => new ThingWithValue(3)]
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

test('for and if', function () {
    assertTemplateResult(
        '+--',
        '{%for item in array%}{% if forloop.first %}+{% else %}-{% endif %}{%endfor%}',
        assigns: ['array' => [1, 2, 3]],
    );
});

test('for else', function () {
    assertTemplateResult('+++', '{%for item in array%}+{%else%}-{%endfor%}', ['array' => [1, 2, 3]]);
    assertTemplateResult('-', '{%for item in array%}+{%else%}-{%endfor%}', ['array' => []]);
    assertTemplateResult('-', '{%for item in array%}+{%else%}-{%endfor%}', ['array' => null]);
});

test('limiting', function () {
    $assigns = ['array' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 0]];
    assertTemplateResult('12', '{%for i in array limit:2 %}{{ i }}{%endfor%}', assigns: $assigns);
    assertTemplateResult('1234', '{%for i in array limit:4 %}{{ i }}{%endfor%}', assigns: $assigns);
    assertTemplateResult('3456', '{%for i in array limit:4 offset:2 %}{{ i }}{%endfor%}', assigns: $assigns);
    assertTemplateResult('3456', '{%for i in array limit: 4 offset: 2 %}{{ i }}{%endfor%}', assigns: $assigns);
    assertTemplateResult('3456', '{%for i in array, limit: 4, offset: 2 %}{{ i }}{%endfor%}', assigns: $assigns);
});

test('limiting with invalid limit', function () {
    $assigns = ['array' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 0]];
    $template = <<<'LIQUID'
      {% for i in array limit: true offset: 1 %}
        {{ i }}
      {% endfor %}
    LIQUID;

    expect(fn () => renderTemplate($template, assigns: $assigns))->toThrow(InvalidArgumentException::class, 'Invalid integer');
});

test('limiting with invalid offset', function () {
    $assigns = ['array' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 0]];
    $template = <<<'LIQUID'
      {% for i in array limit: 1 offset: true %}
        {{ i }}
      {% endfor %}
    LIQUID;

    expect(fn () => renderTemplate($template, assigns: $assigns))->toThrow(InvalidArgumentException::class, 'Invalid integer');
});

test('dynamic variable limiting', function () {
    $assigns = [
        'array' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 0],
        'limit' => 2,
        'offset' => 2,
    ];
    assertTemplateResult('34', '{%for i in array limit: limit offset: offset %}{{ i }}{%endfor%}', assigns: $assigns);
});

test('nested for', function () {
    $assigns = ['array' => [[1, 2], [3, 4], [5, 6]]];
    assertTemplateResult('123456', '{%for item in array%}{%for i in item%}{{ i }}{%endfor%}{%endfor%}', assigns: $assigns);
});

test('offset only', function () {
    $assigns = ['array' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 0]];
    assertTemplateResult('890', '{%for i in array offset:7 %}{{ i }}{%endfor%}', assigns: $assigns);
});

test('pause resume', function () {
    $assigns = ['array' => ['items' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 0]]];

    $markup = <<<'LIQUID'
      {%for i in array.items limit: 3 %}{{i}}{%endfor%}
      next
      {%for i in array.items offset:continue limit: 3 %}{{i}}{%endfor%}
      next
      {%for i in array.items offset:continue limit: 3 %}{{i}}{%endfor%}
      LIQUID;
    $expected = <<<'HTML'
      123
      next
      456
      next
      789
      HTML;

    assertTemplateResult($expected, $markup, assigns: $assigns);
});

test('pause resume limit', function () {
    $assigns = ['array' => ['items' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 0]]];

    $markup = <<<'LIQUID'
      {%for i in array.items limit:3 %}{{i}}{%endfor%}
      next
      {%for i in array.items offset:continue limit:3 %}{{i}}{%endfor%}
      next
      {%for i in array.items offset:continue limit:1 %}{{i}}{%endfor%}
      LIQUID;
    $expected = <<<'HTML'
      123
      next
      456
      next
      7
      HTML;

    assertTemplateResult($expected, $markup, assigns: $assigns);
});

test('pause resume big limit', function () {
    $assigns = ['array' => ['items' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 0]]];

    $markup = <<<'LIQUID'
      {%for i in array.items limit:3 %}{{i}}{%endfor%}
      next
      {%for i in array.items offset:continue limit:3 %}{{i}}{%endfor%}
      next
      {%for i in array.items offset:continue limit:1000 %}{{i}}{%endfor%}
      LIQUID;
    $expected = <<<'HTML'
      123
      next
      456
      next
      7890
      HTML;

    assertTemplateResult($expected, $markup, assigns: $assigns);
});

test('pause resume big offset', function () {
    $assigns = ['array' => ['items' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 0]]];

    $markup = <<<'LIQUID'
      {%for i in array.items limit:3 %}{{i}}{%endfor%}
      next
      {%for i in array.items offset:continue limit:3 %}{{i}}{%endfor%}
      next
      {%for i in array.items offset:1000 limit:3 %}{{i}}{%endfor%}
      LIQUID;
    $expected = <<<'HTML'
      123
      next
      456
      next

      HTML;

    assertTemplateResult($expected, $markup, assigns: $assigns);
});

test('for with break', function () {
    $assigns = ['array' => ['items' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]]];

    assertTemplateResult('', '{% for i in array.items %}{% break %}{% endfor %}', assigns: $assigns);
    assertTemplateResult('1', '{% for i in array.items %}{{ i }}{% break %}{% endfor %}', assigns: $assigns);
    assertTemplateResult('', '{% for i in array.items %}{% break %}{{ i }}{% endfor %}', assigns: $assigns);
    assertTemplateResult('1234', '{% for i in array.items %}{{ i }}{% if i > 3 %}{% break %}{% endif %}{% endfor %}', assigns: $assigns);
    assertTemplateResult(
        '3456',
        '{% for item in array %}{% for i in item %}{% if i == 1 %}{% break %}{% endif %}{{ i }}{% endfor %}{% endfor %}',
        assigns: ['array' => [[1, 2], [3, 4], [5, 6]]]
    );
    assertTemplateResult(
        '12345',
        '{% for i in array.items %}{% if i == 9999 %}{% break %}{% endif %}{{ i }}{% endfor %}',
        assigns: ['array' => ['items' => [1, 2, 3, 4, 5]]]
    );
});

test('for with break after nested loop', function () {
    assertTemplateResult(
        '1-1,1-2,after',
        <<<'LIQUID'
        {% for i in (1..2) -%}
            {% for j in (1..2) -%}
                {{ i }}-{{ j }},
            {%- endfor -%}
            {% break -%}
        {% endfor -%}
        after
        LIQUID,
    );
});

test('for with continue', function () {
    $assigns = ['array' => ['items' => [1, 2, 3, 4, 5]]];

    assertTemplateResult('', '{% for i in array.items %}{% continue %}{% endfor %}', assigns: $assigns);
    assertTemplateResult('12345', '{% for i in array.items %}{{ i }}{% continue %}{% endfor %}', assigns: $assigns);
    assertTemplateResult('', '{% for i in array.items %}{% continue %}{{ i }}{% endfor %}', assigns: $assigns);
    assertTemplateResult('123', '{% for i in array.items %}{% if i > 3 %}{% continue %}{% endif %}{{ i }}{% endfor %}', assigns: $assigns);
    assertTemplateResult('1245', '{% for i in array.items %}{% if i == 3 %}{% continue %}{% else %}{{ i }}{% endif %}{% endfor %}', assigns: $assigns);
    assertTemplateResult(
        '23456',
        '{% for item in array %}{% for i in item %}{% if i == 1 %}{% continue %}{% endif %}{{ i }}{% endfor %}{% endfor %}',
        assigns: ['array' => [[1, 2], [3, 4], [5, 6]]]
    );
    assertTemplateResult('12345', '{% for i in array.items %}{% if i == 9999 %}{% continue %}{% endif %}{{ i }}{% endfor %}', assigns: $assigns);
});

test('for parentloop references parent loop', function () {
    assertTemplateResult(
        '.1|.2|',
        <<<'LIQUID'
        {% for inner in outer -%}
        {{ forloop.parentloop.index }}.{{ forloop.index }}|
        {%- endfor -%}
        LIQUID,
        ['outer' => [[1, 1, 1], [1, 1, 1]]],
    );
});

test('bad variable naming in for loop', function () {
    expect(fn () => renderTemplate('{% for a/b in x %}{% endfor %}'))->toThrow(SyntaxException::class);
});

test('spacing with variable naming in for loop', function () {
    assertTemplateResult('12345', '{% for       item   in   items %}{{item}}{% endfor %}', assigns: ['items' => [1, 2, 3, 4, 5]]);
});

test('iterate drop with no limit applied', function () {
    $loader = new LoaderDrop([1, 2, 3, 4, 5]);

    assertTemplateResult('12345', '{% for item in items %}{{item}}{% endfor %}', assigns: ['items' => $loader]);
});

test('iterate drop with limit applied', function () {
    $loader = new LoaderDrop([1, 2, 3, 4, 5]);

    assertTemplateResult('34', '{% for item in items offset:2 limit:2 %}{{item}}{% endfor %}', assigns: ['items' => $loader]);
});

test('for cleans up registers', function () {
    $context = new Context(staticEnvironment: ['drop' => new ErrorDrop()]);

    expect(fn () => Template::parse('{% for i in (1..2) %}{{ drop.standard_error }}{% endfor %}')->render($context))->toThrow(Exception::class);

    expect($context->getRegister('for_stack'))->toBe([]);
});
