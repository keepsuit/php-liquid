<?php

use Keepsuit\Liquid\Performance\benchmarks\Support\ComplexThemeFixture;

test('complex theme fixture renders the deterministic collection page', function () {
    $environment = ComplexThemeFixture::environment();

    $template = $environment->parseTemplate(ComplexThemeFixture::rootTemplateName());

    $rendered = $template->render(ComplexThemeFixture::newRenderContext($environment));

    expect($rendered)
        ->toContain('24 products selected')
        ->toContain('data-handle="sketchbook"')
        ->and(hash('sha256', $rendered))->toBe('17ee8672cb258106bb58a0642a4fa55cabc66163f0b27cdb37c9f3392fb80a45');
});
