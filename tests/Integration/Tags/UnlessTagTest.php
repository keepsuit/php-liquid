<?php

describe('rendering with template backends', function () {
    test('unless', function (bool $compiled) {
        assertTemplateResult(
            '  ',
            ' {% unless true %} this text should not go into the output {% endunless %} ', compiled: $compiled);
        assertTemplateResult(
            '  this text should go into the output  ',
            ' {% unless false %} this text should go into the output {% endunless %} ',
            compiled: $compiled);
        assertTemplateResult(
            '  you rock ?',
            '{% unless true %} you suck {% endunless %} {% unless false %} you rock {% endunless %}?', compiled: $compiled);
    });

    test('unless else', function (bool $compiled) {
        assertTemplateResult(' YES ', '{% unless true %} NO {% else %} YES {% endunless %}', compiled: $compiled);
        assertTemplateResult(' YES ', '{% unless false %} YES {% else %} NO {% endunless %}', compiled: $compiled);
        assertTemplateResult(' YES ', '{% unless "foo" %} NO {% else %} YES {% endunless %}', compiled: $compiled);
    });

    test('unless in loop', function (bool $compiled) {
        assertTemplateResult(
            '23',
            '{% for i in choices %}{% unless i %}{{ forloop.index }}{% endunless %}{% endfor %}',
            staticData: ['choices' => [1, null, false]], compiled: $compiled);
    });

    test('unless else in loop', function (bool $compiled) {
        assertTemplateResult(
            ' TRUE  2  3 ',
            '{% for i in choices %}{% unless i %} {{ forloop.index }} {% else %} TRUE {% endunless %}{% endfor %}',
            staticData: ['choices' => [1, null, false]], compiled: $compiled);
    });
})->with('template backends');
