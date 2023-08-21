<?php

test('unless', function () {
    assertTemplateResult(
        '  ',
        ' {% unless true %} this text should not go into the output {% endunless %} '
    );
    assertTemplateResult(
        '  this text should go into the output  ',
        ' {% unless false %} this text should go into the output {% endunless %} ',
    );
    assertTemplateResult(
        '  you rock ?',
        '{% unless true %} you suck {% endunless %} {% unless false %} you rock {% endunless %}?'
    );
});

test('unless else', function () {
    assertTemplateResult(' YES ', '{% unless true %} NO {% else %} YES {% endunless %}');
    assertTemplateResult(' YES ', '{% unless false %} YES {% else %} NO {% endunless %}');
    assertTemplateResult(' YES ', '{% unless "foo" %} NO {% else %} YES {% endunless %}');
});

test('unless in loop', function () {
    assertTemplateResult(
        '23',
        '{% for i in choices %}{% unless i %}{{ forloop.index }}{% endunless %}{% endfor %}',
        assigns: ['choices' => [1, null, false]]
    );
});

test('unless else in loop', function () {
    assertTemplateResult(
        ' TRUE  2  3 ',
        '{% for i in choices %}{% unless i %} {{ forloop.index }} {% else %} TRUE {% endunless %}{% endfor %}',
        assigns: ['choices' => [1, null, false]]
    );
});
