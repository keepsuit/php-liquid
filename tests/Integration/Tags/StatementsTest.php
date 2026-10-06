<?php

describe('rendering with template backends', function () {
    test('true equal true', function (bool $compiled) {
        assertTemplateResult('  true  ', ' {% if true == true %} true {% else %} false {% endif %} ', compiled: $compiled);
    });

    test('true not equal true', function (bool $compiled) {
        assertTemplateResult('  false  ', ' {% if true != true %} true {% else %} false {% endif %} ', compiled: $compiled);
    });

    test('zero greater than zero', function (bool $compiled) {
        assertTemplateResult('  false  ', ' {% if 0 > 0 %} true {% else %} false {% endif %} ', compiled: $compiled);
    });

    test('one greater than zero', function (bool $compiled) {
        assertTemplateResult('  true  ', ' {% if 1 > 0 %} true {% else %} false {% endif %} ', compiled: $compiled);
    });

    test('zero lower than one', function (bool $compiled) {
        assertTemplateResult('  true  ', ' {% if 0 < 1 %} true {% else %} false {% endif %} ', compiled: $compiled);
    });

    test('zero lower than or equal to zero', function (bool $compiled) {
        assertTemplateResult('  true  ', ' {% if 0 <= 0 %} true {% else %} false {% endif %} ', compiled: $compiled);
    });

    test('zero lower than or equal to null', function (bool $compiled) {
        assertTemplateResult('  false  ', ' {% if null <= 0 %} true {% else %} false {% endif %} ', compiled: $compiled);
        assertTemplateResult('  false  ', ' {% if 0 <= null %} true {% else %} false {% endif %} ', compiled: $compiled);
    });

    test('zero greater than or equal to zero', function (bool $compiled) {
        assertTemplateResult('  true  ', ' {% if 0 >= 0 %} true {% else %} false {% endif %} ', compiled: $compiled);
    });

    test('strings', function (bool $compiled) {
        assertTemplateResult('  true  ', " {% if 'test' == 'test' %} true {% else %} false {% endif %} ", compiled: $compiled);
    });

    test('strings not equal', function (bool $compiled) {
        assertTemplateResult('  false  ', " {% if 'test' != 'test' %} true {% else %} false {% endif %} ", compiled: $compiled);
    });

    test('var strings equal', function (bool $compiled) {
        assertTemplateResult('  true  ', ' {% if var == "hello there!" %} true {% else %} false {% endif %} ', ['var' => 'hello there!'], compiled: $compiled);
    });

    test('var strings not equal', function (bool $compiled) {
        assertTemplateResult('  true  ', ' {% if "hello" != var %} true {% else %} false {% endif %} ', ['var' => 'hello there!'], compiled: $compiled);
    });

    test('collection is empty', function (bool $compiled) {
        assertTemplateResult('  true  ', ' {% if array == empty %} true {% else %} false {% endif %} ', ['array' => []], compiled: $compiled);
    });

    test('collection is not empty', function (bool $compiled) {
        assertTemplateResult('  false  ', ' {% if array == empty %} true {% else %} false {% endif %} ', ['array' => [1, 2, 3]], compiled: $compiled);
    });

    test('string is empty', function (bool $compiled) {
        assertTemplateResult('  true  ', ' {% if var == empty %} true {% else %} false {% endif %} ', ['var' => ''], compiled: $compiled);
    });

    test('string is not empty', function (bool $compiled) {
        assertTemplateResult('  false  ', ' {% if var == empty %} true {% else %} false {% endif %} ', ['var' => 'hello'], compiled: $compiled);
    });

    test('empty only matches empty strings and collections', function (bool $compiled, mixed $value, bool $expected) {
        assertTemplateResult($expected ? 'T' : 'F', '{% if var == empty %}T{% else %}F{% endif %}', ['var' => $value], compiled: $compiled);
        assertTemplateResult($expected ? 'F' : 'T', '{% if var != empty %}T{% else %}F{% endif %}', ['var' => $value], compiled: $compiled);
    })->with([
        'empty string' => ['', true],
        'empty array' => [[], true],
        'whitespace' => [' ', false],
        'zero' => [0, false],
        'zero string' => ['0', false],
        'false' => [false, false],
        'null' => [null, false],
        'string' => ['a', false],
        'array' => [[1], false],
        'empty iterable' => [new \Keepsuit\Liquid\Tests\Stubs\Collection([]), true],
        'iterable' => [new \Keepsuit\Liquid\Tests\Stubs\Collection([1]), false],
    ]);

    test('blank matches nil, false, whitespace strings and empty collections', function (bool $compiled, mixed $value, bool $expected) {
        assertTemplateResult($expected ? 'T' : 'F', '{% if var == blank %}T{% else %}F{% endif %}', ['var' => $value], compiled: $compiled);
        assertTemplateResult($expected ? 'T' : 'F', '{% if blank == var %}T{% else %}F{% endif %}', ['var' => $value], compiled: $compiled);
        assertTemplateResult($expected ? 'F' : 'T', '{% if var != blank %}T{% else %}F{% endif %}', ['var' => $value], compiled: $compiled);
    })->with([
        'empty string' => ['', true],
        'whitespace' => ["  \n", true],
        'empty array' => [[], true],
        'null' => [null, true],
        'false' => [false, true],
        'true' => [true, false],
        'zero' => [0, false],
        'string' => ['a', false],
        'array' => [[1], false],
        'empty iterable' => [new \Keepsuit\Liquid\Tests\Stubs\Collection([]), true],
        'iterable' => [new \Keepsuit\Liquid\Tests\Stubs\Collection([1]), false],
    ]);

    test('null', function (bool $compiled) {
        assertTemplateResult('  true  ', ' {% if var == nil %} true {% else %} false {% endif %} ', ['var' => null], compiled: $compiled);
        assertTemplateResult('  true  ', ' {% if var == null %} true {% else %} false {% endif %} ', ['var' => null], compiled: $compiled);
    });
})->with('template backends');
