<?php

test('true equal true', function () {
    assertTemplateResult('  true  ', ' {% if true == true %} true {% else %} false {% endif %} ');
});

test('true not equal true', function () {
    assertTemplateResult('  false  ', ' {% if true != true %} true {% else %} false {% endif %} ');
});

test('zero greater than zero', function () {
    assertTemplateResult('  false  ', ' {% if 0 > 0 %} true {% else %} false {% endif %} ');
});

test('one greater than zero', function () {
    assertTemplateResult('  true  ', ' {% if 1 > 0 %} true {% else %} false {% endif %} ');
});

test('zero lower than one', function () {
    assertTemplateResult('  true  ', ' {% if 0 < 1 %} true {% else %} false {% endif %} ');
});

test('zero lower than or equal to zero', function () {
    assertTemplateResult('  true  ', ' {% if 0 <= 0 %} true {% else %} false {% endif %} ');
});

test('zero lower than or equal to null', function () {
    assertTemplateResult('  false  ', ' {% if null <= 0 %} true {% else %} false {% endif %} ');
    assertTemplateResult('  false  ', ' {% if 0 <= null %} true {% else %} false {% endif %} ');
});

test('zero greater than or equal to zero', function () {
    assertTemplateResult('  true  ', ' {% if 0 >= 0 %} true {% else %} false {% endif %} ');
});

test('strings', function () {
    assertTemplateResult('  true  ', " {% if 'test' == 'test' %} true {% else %} false {% endif %} ");
});

test('strings not equal', function () {
    assertTemplateResult('  false  ', " {% if 'test' != 'test' %} true {% else %} false {% endif %} ");
});

test('var strings equal', function () {
    assertTemplateResult('  true  ', ' {% if var == "hello there!" %} true {% else %} false {% endif %} ', ['var' => 'hello there!']);
});

test('var strings not equal', function () {
    assertTemplateResult('  true  ', ' {% if "hello" != var %} true {% else %} false {% endif %} ', ['var' => 'hello there!']);
});

test('collection is empty', function () {
    assertTemplateResult('  true  ', ' {% if array == empty %} true {% else %} false {% endif %} ', ['array' => []]);
});

test('collection is not empty', function () {
    assertTemplateResult('  false  ', ' {% if array == empty %} true {% else %} false {% endif %} ', ['array' => [1, 2, 3]]);
});

test('string is empty', function () {
    assertTemplateResult('  true  ', ' {% if var == empty %} true {% else %} false {% endif %} ', ['var' => '']);
});

test('string is not empty', function () {
    assertTemplateResult('  false  ', ' {% if var == empty %} true {% else %} false {% endif %} ', ['var' => 'hello']);
});

test('null', function () {
    assertTemplateResult('  true  ', ' {% if var == nil %} true {% else %} false {% endif %} ', ['var' => null]);
    assertTemplateResult('  true  ', ' {% if var == null %} true {% else %} false {% endif %} ', ['var' => null]);
});
