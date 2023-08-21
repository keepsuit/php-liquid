<?php

use Keepsuit\Liquid\Exceptions\SyntaxException;

afterEach(function () {
    \Keepsuit\Liquid\Condition::resetOperators();
});

test('if', function () {
    assertTemplateResult('  ', ' {% if false %} this text should not go into the output {% endif %} ');
    assertTemplateResult('  this text should go into the output  ', ' {% if true %} this text should go into the output {% endif %} ');
    assertTemplateResult('  you rock ?', '{% if false %} you suck {% endif %} {% if true %} you rock {% endif %}?');
});

test('literal comparisons', function () {
    assertTemplateResult(' NO ', '{% assign v = false %}{% if v %} YES {% else %} NO {% endif %}');
    assertTemplateResult(' YES ', '{% assign v = nil %}{% if v == nil %} YES {% else %} NO {% endif %}');
});

test('if else', function () {
    assertTemplateResult(' YES ', '{% if false %} NO {% else %} YES {% endif %}');
    assertTemplateResult(' YES ', '{% if true %} YES {% else %} NO {% endif %}');
    assertTemplateResult(' YES ', '{% if "foo" %} YES {% else %} NO {% endif %}');
});

test('if boolean', function () {
    assertTemplateResult(' YES ', '{% if var %} YES {% endif %}', ['var' => true]);
});

test('if or', function () {
    assertTemplateResult(' YES ', '{% if a or b %} YES {% endif %}', ['a' => true, 'b' => true]);
    assertTemplateResult(' YES ', '{% if a or b %} YES {% endif %}', ['a' => true, 'b' => false]);
    assertTemplateResult(' YES ', '{% if a or b %} YES {% endif %}', ['a' => false, 'b' => true]);
    assertTemplateResult('', '{% if a or b %} YES {% endif %}', ['a' => false, 'b' => false]);

    assertTemplateResult(' YES ', '{% if a or b or c %} YES {% endif %}', ['a' => false, 'b' => false, 'c' => true]);
    assertTemplateResult('', '{% if a or b or c %} YES {% endif %}', ['a' => false, 'b' => false, 'c' => false]);
});

test('if or with operators', function () {
    assertTemplateResult(' YES ', '{% if a == true or b == true %} YES {% endif %}', ['a' => true, 'b' => true]);
    assertTemplateResult(' YES ', '{% if a == true or b == false %} YES {% endif %}', ['a' => true, 'b' => true]);
    assertTemplateResult('', '{% if a == false or b == false %} YES {% endif %}', ['a' => true, 'b' => true]);
});

test('comparison of strings containing and or or', function () {
    $awfulMarkup = "a == 'and' and b == 'or' and c == 'foo and bar' and d == 'bar or baz' and e == 'foo' and foo and bar";
    $assigns = ['a' => 'and', 'b' => 'or', 'c' => 'foo and bar', 'd' => 'bar or baz', 'e' => 'foo', 'foo' => true, 'bar' => true];
    assertTemplateResult(' YES ', "{% if $awfulMarkup %} YES {% endif %}", assigns: $assigns);
});

test('comparison of expressions starting with and or or', function () {
    $assigns = ['order' => ['items_count' => 0], 'android' => ['name' => 'Roy']];
    assertTemplateResult('YES', "{% if android.name == 'Roy' %}YES{% endif %}", assigns: $assigns);
    assertTemplateResult('YES', '{% if order.items_count == 0 %}YES{% endif %}', assigns: $assigns);
});

test('if and', function () {
    assertTemplateResult(' YES ', '{% if true and true %} YES {% endif %}');
    assertTemplateResult('', '{% if false and true %} YES {% endif %}');
    assertTemplateResult('', '{% if true and false %} YES {% endif %}');
});

test('hash miss generates false', function () {
    assertTemplateResult('', '{% if foo.bar %} NO {% endif %}', assigns: ['foo' => []]);
});

test('if from variable', function () {
    assertTemplateResult('', '{% if var %} NO {% endif %}', ['var' => false]);
    assertTemplateResult('', '{% if var %} NO {% endif %}', ['var' => null]);
    assertTemplateResult('', '{% if foo.bar %} NO {% endif %}', ['foo' => ['bar' => false]]);
    assertTemplateResult('', '{% if foo.bar %} NO {% endif %}', ['foo' => []]);
    assertTemplateResult('', '{% if foo.bar %} NO {% endif %}', ['foo' => null]);
    assertTemplateResult('', '{% if foo.bar %} NO {% endif %}', ['foo' => true]);

    assertTemplateResult(' YES ', '{% if var %} YES {% endif %}', ['var' => 'text']);
    assertTemplateResult(' YES ', '{% if var %} YES {% endif %}', ['var' => true]);
    assertTemplateResult(' YES ', '{% if var %} YES {% endif %}', ['var' => 1]);
    assertTemplateResult(' YES ', '{% if var %} YES {% endif %}', ['var' => []]);
    assertTemplateResult(' YES ', '{% if "foo" %} YES {% endif %}');
    assertTemplateResult(' YES ', '{% if foo.bar %} YES {% endif %}', ['foo' => ['bar' => true]]);
    assertTemplateResult(' YES ', '{% if foo.bar %} YES {% endif %}', ['foo' => ['bar' => 'text']]);
    assertTemplateResult(' YES ', '{% if foo.bar %} YES {% endif %}', ['foo' => ['bar' => 1]]);
    assertTemplateResult(' YES ', '{% if foo.bar %} YES {% endif %}', ['foo' => ['bar' => []]]);

    assertTemplateResult(' YES ', '{% if var %} NO {% else %} YES {% endif %}', ['var' => false]);
    assertTemplateResult(' YES ', '{% if var %} NO {% else %} YES {% endif %}', ['var' => null]);
    assertTemplateResult(' YES ', '{% if var %} YES {% else %} NO {% endif %}', ['var' => true]);
    assertTemplateResult(' YES ', '{% if "foo" %} YES {% else %} NO {% endif %}', ['var' => 'text']);

    assertTemplateResult(' YES ', '{% if foo.bar %} NO {% else %} YES {% endif %}', ['foo' => ['bar' => false]]);
    assertTemplateResult(' YES ', '{% if foo.bar %} YES {% else %} NO {% endif %}', ['foo' => ['bar' => true]]);
    assertTemplateResult(' YES ', '{% if foo.bar %} YES {% else %} NO {% endif %}', ['foo' => ['bar' => 'text']]);
    assertTemplateResult(' YES ', '{% if foo.bar %} NO {% else %} YES {% endif %}', ['foo' => ['notbar' => true]]);
    assertTemplateResult(' YES ', '{% if foo.bar %} NO {% else %} YES {% endif %}', ['foo' => []]);
    assertTemplateResult(' YES ', '{% if foo.bar %} NO {% else %} YES {% endif %}', ['notfoo' => [], 'bar' => true]);
});

test('nested if', function () {
    assertTemplateResult('', '{% if false %}{% if false %} NO {% endif %}{% endif %}');
    assertTemplateResult('', '{% if false %}{% if true %} NO {% endif %}{% endif %}');
    assertTemplateResult('', '{% if true %}{% if false %} NO {% endif %}{% endif %}');
    assertTemplateResult(' YES ', '{% if true %}{% if true %} YES {% endif %}{% endif %}');

    assertTemplateResult(' YES ', '{% if true %}{% if true %} YES {% else %} NO {% endif %}{% else %} NO {% endif %}');
    assertTemplateResult(' YES ', '{% if true %}{% if false %} NO {% else %} YES {% endif %}{% else %} NO {% endif %}');
    assertTemplateResult(' YES ', '{% if false %}{% if true %} NO {% else %} NONO {% endif %}{% else %} YES {% endif %}');
});

test('comparisons on null', function () {
    assertTemplateResult('', '{% if null < 10 %} NO {% endif %}');
    assertTemplateResult('', '{% if null <= 10 %} NO {% endif %}');
    assertTemplateResult('', '{% if null >= 10 %} NO {% endif %}');
    assertTemplateResult('', '{% if null > 10 %} NO {% endif %}');

    assertTemplateResult('', '{% if 10 < null %} NO {% endif %}');
    assertTemplateResult('', '{% if 10 <= null %} NO {% endif %}');
    assertTemplateResult('', '{% if 10 >= null %} NO {% endif %}');
    assertTemplateResult('', '{% if 10 > null %} NO {% endif %}');
});

test('else if', function () {
    assertTemplateResult('0', '{% if 0 == 0 %}0{% elsif 1 == 1%}1{% else %}2{% endif %}');
    assertTemplateResult('1', '{% if 0 != 0 %}0{% elsif 1 == 1%}1{% else %}2{% endif %}');
    assertTemplateResult('2', '{% if 0 != 0 %}0{% elsif 1 != 1%}1{% else %}2{% endif %}');

    assertTemplateResult('elsif', '{% if false %}if{% elsif true %}elsif{% endif %}');
});

test('throw exception with no expression', function () {
    expect(fn () => renderTemplate('{% if %}'))->toThrow(SyntaxException::class);
});

test('operators are isolated', function () {
    expect(fn () => renderTemplate('{% if 1 or throw or or 1 %}yes{% endif %}'))->toThrow(SyntaxException::class);
});

test('multiple conditions', function (string $result, array $assigns) {
    assertTemplateResult($result, '{% if a or b and c %}true{% else %}false{% endif %}', assigns: $assigns);
})->with([
    ['true', ['a' => true, 'b' => true, 'c' => true]],
    ['true', ['a' => true, 'b' => true, 'c' => false]],
    ['true', ['a' => true, 'b' => false, 'c' => true]],
    ['true', ['a' => true, 'b' => false, 'c' => false]],
    ['true', ['a' => false, 'b' => true, 'c' => true]],
    ['false', ['a' => false, 'b' => true, 'c' => false]],
    ['false', ['a' => false, 'b' => false, 'c' => true]],
    ['false', ['a' => false, 'b' => false, 'c' => false]],
]);
