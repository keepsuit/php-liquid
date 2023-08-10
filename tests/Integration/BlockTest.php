<?php

use Keepsuit\Liquid\SyntaxException;
use Keepsuit\Liquid\Template;

test('unexpected end tag', function () {
    expect(fn () => parseTemplate('{% if true %}{% endunless %}'))
        ->toThrow(SyntaxException::class, "'endunless' is not a valid delimiter for if tags. use endif");
});

test('with custom tag block', function () {
    Template::registerTag(\Keepsuit\Liquid\Tests\Stubs\TestTagBlockTag::class);
    assertTemplateResult(
        '',
        '{% testblock %}{% endtestblock %}'
    );
});

test('custom tag block have a default render method', function () {
    Template::registerTag(\Keepsuit\Liquid\Tests\Stubs\TestTagBlockTag::class);

    assertTemplateResult(
        ' bla ',
        '{% testblock %} bla {% endtestblock %}'
    );
});
