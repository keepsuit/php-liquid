<?php

use Keepsuit\Liquid\Exceptions\SyntaxException;

afterEach(function () {
    \Keepsuit\Liquid\Condition\Condition::resetOperators();
});

test('throw exception with no expression', function () {
    expect(fn () => renderTemplate('{% if %}'))->toThrow(SyntaxException::class);
});

test('operators are isolated', function () {
    expect(fn () => renderTemplate('{% if 1 or throw or or 1 %}yes{% endif %}'))->toThrow(SyntaxException::class);
});

describe('rendering with template backends', function () {
    test('if', function (bool $compiled) {
        assertTemplateResult('  ', ' {% if false %} this text should not go into the output {% endif %} ', compiled: $compiled);
        assertTemplateResult('  this text should go into the output  ', ' {% if true %} this text should go into the output {% endif %} ', compiled: $compiled);
        assertTemplateResult('  you rock ?', '{% if false %} you suck {% endif %} {% if true %} you rock {% endif %}?', compiled: $compiled);
    });

    test('literal comparisons', function (bool $compiled) {
        assertTemplateResult(' NO ', '{% assign v = false %}{% if v %} YES {% else %} NO {% endif %}', compiled: $compiled);
        assertTemplateResult(' YES ', '{% assign v = nil %}{% if v == nil %} YES {% else %} NO {% endif %}', compiled: $compiled);
    });

    test('comparison branch selection matches Shopify value types', function (bool $compiled) {
        assertTemplateResult('equal', '{% if value == 5.0 %}equal{% else %}different{% endif %}', ['value' => 5], compiled: $compiled);
        assertTemplateResult('different', '{% if value == "5" %}equal{% else %}different{% endif %}', ['value' => 5], compiled: $compiled);
        assertTemplateResult('less', "{% if value < '9' %}less{% else %}not less{% endif %}", ['value' => '10'], compiled: $compiled);
        assertTemplateResult('different', '{% unless value == "5" %}different{% else %}equal{% endunless %}', ['value' => 5], compiled: $compiled);
    });

    test('contains on ranges matches numbers within bounds', function (bool $compiled) {
        assertTemplateResult('ABF', "{% assign x = (1..3) %}{% if x contains 2 %}A{% endif %}{% if (1..3) contains 2 %}B{% endif %}{% if (1..3) contains 2.5 %}F{% endif %}{% if (1..3) contains '2' %}S{% endif %}{% if (1..3) contains 4 %}O{% endif %}", compiled: $compiled);
    });

    test('contains finds hash keys while preserving list values', function (bool $compiled) {
        assertTemplateResult('key found', "{% if value contains 'a' %}key found{% else %}missing{% endif %}", ['value' => ['a' => 1]], compiled: $compiled);
        assertTemplateResult('value found', "{% if value contains 'a' %}value found{% else %}missing{% endif %}", ['value' => ['a', 'b']], compiled: $compiled);
        assertTemplateResult('missing', "{% if value contains 'a' %}found{% else %}missing{% endif %}", ['value' => ['b']], compiled: $compiled);
    });

    test('if else', function (bool $compiled) {
        assertTemplateResult(' YES ', '{% if false %} NO {% else %} YES {% endif %}', compiled: $compiled);
        assertTemplateResult(' YES ', '{% if true %} YES {% else %} NO {% endif %}', compiled: $compiled);
        assertTemplateResult(' YES ', '{% if "foo" %} YES {% else %} NO {% endif %}', compiled: $compiled);
    });

    test('if boolean', function (bool $compiled) {
        assertTemplateResult(' YES ', '{% if var %} YES {% endif %}', ['var' => true], compiled: $compiled);
    });

    test('if or', function (bool $compiled) {
        assertTemplateResult(' YES ', '{% if a or b %} YES {% endif %}', ['a' => true, 'b' => true], compiled: $compiled);
        assertTemplateResult(' YES ', '{% if a or b %} YES {% endif %}', ['a' => true, 'b' => false], compiled: $compiled);
        assertTemplateResult(' YES ', '{% if a or b %} YES {% endif %}', ['a' => false, 'b' => true], compiled: $compiled);
        assertTemplateResult('', '{% if a or b %} YES {% endif %}', ['a' => false, 'b' => false], compiled: $compiled);

        assertTemplateResult(' YES ', '{% if a or b or c %} YES {% endif %}', ['a' => false, 'b' => false, 'c' => true], compiled: $compiled);
        assertTemplateResult('', '{% if a or b or c %} YES {% endif %}', ['a' => false, 'b' => false, 'c' => false], compiled: $compiled);
    });

    test('if or with operators', function (bool $compiled) {
        assertTemplateResult(' YES ', '{% if a == true or b == true %} YES {% endif %}', ['a' => true, 'b' => true], compiled: $compiled);
        assertTemplateResult(' YES ', '{% if a == true or b == false %} YES {% endif %}', ['a' => true, 'b' => true], compiled: $compiled);
        assertTemplateResult('', '{% if a == false or b == false %} YES {% endif %}', ['a' => true, 'b' => true], compiled: $compiled);
    });

    test('comparison of strings containing and or or', function (bool $compiled) {
        $awfulMarkup = "a == 'and' and b == 'or' and c == 'foo and bar' and d == 'bar or baz' and e == 'foo' and foo and bar";
        $assigns = ['a' => 'and', 'b' => 'or', 'c' => 'foo and bar', 'd' => 'bar or baz', 'e' => 'foo', 'foo' => true, 'bar' => true];
        assertTemplateResult(' YES ', "{% if $awfulMarkup %} YES {% endif %}", staticData: $assigns, compiled: $compiled);
    });

    test('comparison of expressions starting with and or or', function (bool $compiled) {
        $assigns = ['order' => ['items_count' => 0], 'android' => ['name' => 'Roy']];
        assertTemplateResult('YES', "{% if android.name == 'Roy' %}YES{% endif %}", staticData: $assigns, compiled: $compiled);
        assertTemplateResult('YES', '{% if order.items_count == 0 %}YES{% endif %}', staticData: $assigns, compiled: $compiled);
    });

    test('if and', function (bool $compiled) {
        assertTemplateResult(' YES ', '{% if true and true %} YES {% endif %}', compiled: $compiled);
        assertTemplateResult('', '{% if false and true %} YES {% endif %}', compiled: $compiled);
        assertTemplateResult('', '{% if true and false %} YES {% endif %}', compiled: $compiled);
    });

    test('hash miss generates false', function (bool $compiled) {
        assertTemplateResult('', '{% if foo.bar %} NO {% endif %}', staticData: ['foo' => []], compiled: $compiled);
    });

    test('if from variable', function (bool $compiled) {
        assertTemplateResult('', '{% if var %} NO {% endif %}', ['var' => false], compiled: $compiled);
        assertTemplateResult('', '{% if var %} NO {% endif %}', ['var' => null], compiled: $compiled);
        assertTemplateResult('', '{% if foo.bar %} NO {% endif %}', ['foo' => ['bar' => false]], compiled: $compiled);
        assertTemplateResult('', '{% if foo.bar %} NO {% endif %}', ['foo' => []], compiled: $compiled);
        assertTemplateResult('', '{% if foo.bar %} NO {% endif %}', ['foo' => null], compiled: $compiled);
        assertTemplateResult('', '{% if foo.bar %} NO {% endif %}', ['foo' => true], compiled: $compiled);

        assertTemplateResult(' YES ', '{% if var %} YES {% endif %}', ['var' => 'text'], compiled: $compiled);
        assertTemplateResult(' YES ', '{% if var %} YES {% endif %}', ['var' => true], compiled: $compiled);
        assertTemplateResult(' YES ', '{% if var %} YES {% endif %}', ['var' => 1], compiled: $compiled);
        assertTemplateResult(' YES ', '{% if var %} YES {% endif %}', ['var' => []], compiled: $compiled);
        assertTemplateResult(' YES ', '{% if "foo" %} YES {% endif %}', compiled: $compiled);
        assertTemplateResult(' YES ', '{% if foo.bar %} YES {% endif %}', ['foo' => ['bar' => true]], compiled: $compiled);
        assertTemplateResult(' YES ', '{% if foo.bar %} YES {% endif %}', ['foo' => ['bar' => 'text']], compiled: $compiled);
        assertTemplateResult(' YES ', '{% if foo.bar %} YES {% endif %}', ['foo' => ['bar' => 1]], compiled: $compiled);
        assertTemplateResult(' YES ', '{% if foo.bar %} YES {% endif %}', ['foo' => ['bar' => []]], compiled: $compiled);

        assertTemplateResult(' YES ', '{% if var %} NO {% else %} YES {% endif %}', ['var' => false], compiled: $compiled);
        assertTemplateResult(' YES ', '{% if var %} NO {% else %} YES {% endif %}', ['var' => null], compiled: $compiled);
        assertTemplateResult(' YES ', '{% if var %} YES {% else %} NO {% endif %}', ['var' => true], compiled: $compiled);
        assertTemplateResult(' YES ', '{% if "foo" %} YES {% else %} NO {% endif %}', ['var' => 'text'], compiled: $compiled);

        assertTemplateResult(' YES ', '{% if foo.bar %} NO {% else %} YES {% endif %}', ['foo' => ['bar' => false]], compiled: $compiled);
        assertTemplateResult(' YES ', '{% if foo.bar %} YES {% else %} NO {% endif %}', ['foo' => ['bar' => true]], compiled: $compiled);
        assertTemplateResult(' YES ', '{% if foo.bar %} YES {% else %} NO {% endif %}', ['foo' => ['bar' => 'text']], compiled: $compiled);
        assertTemplateResult(' YES ', '{% if foo.bar %} NO {% else %} YES {% endif %}', ['foo' => ['notbar' => true]], compiled: $compiled);
        assertTemplateResult(' YES ', '{% if foo.bar %} NO {% else %} YES {% endif %}', ['foo' => []], compiled: $compiled);
        assertTemplateResult(' YES ', '{% if foo.bar %} NO {% else %} YES {% endif %}', ['notfoo' => [], 'bar' => true], compiled: $compiled);
    });

    test('if undefined variable', function (bool $compiled, bool $strict) {
        assertTemplateResult(' NO ', '{% if var %} YES {% else %} NO {% endif %}', strictVariables: $strict, compiled: $compiled);
        assertTemplateResult(' YES ', '{% if var == null %} YES {% else %} NO {% endif %}', strictVariables: $strict, compiled: $compiled);
        assertTemplateResult(' NO ', '{% if var == false %} YES {% else %} NO {% endif %}', strictVariables: $strict, compiled: $compiled);
    })->with([
        'default' => false,
        'strict' => true,
    ]);

    test('nested if', function (bool $compiled) {
        assertTemplateResult('', '{% if false %}{% if false %} NO {% endif %}{% endif %}', compiled: $compiled);
        assertTemplateResult('', '{% if false %}{% if true %} NO {% endif %}{% endif %}', compiled: $compiled);
        assertTemplateResult('', '{% if true %}{% if false %} NO {% endif %}{% endif %}', compiled: $compiled);
        assertTemplateResult(' YES ', '{% if true %}{% if true %} YES {% endif %}{% endif %}', compiled: $compiled);

        assertTemplateResult(' YES ', '{% if true %}{% if true %} YES {% else %} NO {% endif %}{% else %} NO {% endif %}', compiled: $compiled);
        assertTemplateResult(' YES ', '{% if true %}{% if false %} NO {% else %} YES {% endif %}{% else %} NO {% endif %}', compiled: $compiled);
        assertTemplateResult(' YES ', '{% if false %}{% if true %} NO {% else %} NONO {% endif %}{% else %} YES {% endif %}', compiled: $compiled);
    });

    test('comparisons on null', function (bool $compiled) {
        assertTemplateResult('', '{% if null < 10 %} NO {% endif %}', compiled: $compiled);
        assertTemplateResult('', '{% if null <= 10 %} NO {% endif %}', compiled: $compiled);
        assertTemplateResult('', '{% if null >= 10 %} NO {% endif %}', compiled: $compiled);
        assertTemplateResult('', '{% if null > 10 %} NO {% endif %}', compiled: $compiled);

        assertTemplateResult('', '{% if 10 < null %} NO {% endif %}', compiled: $compiled);
        assertTemplateResult('', '{% if 10 <= null %} NO {% endif %}', compiled: $compiled);
        assertTemplateResult('', '{% if 10 >= null %} NO {% endif %}', compiled: $compiled);
        assertTemplateResult('', '{% if 10 > null %} NO {% endif %}', compiled: $compiled);
    });

    test('else if', function (bool $compiled) {
        assertTemplateResult('0', '{% if 0 == 0 %}0{% elsif 1 == 1%}1{% else %}2{% endif %}', compiled: $compiled);
        assertTemplateResult('1', '{% if 0 != 0 %}0{% elsif 1 == 1%}1{% else %}2{% endif %}', compiled: $compiled);
        assertTemplateResult('2', '{% if 0 != 0 %}0{% elsif 1 != 1%}1{% else %}2{% endif %}', compiled: $compiled);

        assertTemplateResult('elsif', '{% if false %}if{% elsif true %}elsif{% endif %}', compiled: $compiled);
    });

    test('multiple conditions', function (bool $compiled, string $result, array $assigns) {
        assertTemplateResult($result, '{% if a or b and c %}true{% else %}false{% endif %}', staticData: $assigns, compiled: $compiled);
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
})->with('template backends');
