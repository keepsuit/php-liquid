<?php

use Keepsuit\Liquid\Exceptions\SyntaxException;

beforeEach(function () {
    $this->templateFactory = \Keepsuit\Liquid\TemplateFactory::new();
});

test('unexpected end tag', function () {
    expect(fn () => renderTemplate('{% if true %}{% endunless %}'))
        ->toThrow(SyntaxException::class, "'endunless' is not a valid delimiter for if tags. use endif");
});

test('with custom tag block', function () {
    $this->templateFactory->registerTag(\Keepsuit\Liquid\Tests\Stubs\TestTagBlockTag::class);

    assertTemplateResult(
        '',
        '{% testblock %}{% endtestblock %}',
        factory: $this->templateFactory
    );
});
