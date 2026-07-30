<?php

use Keepsuit\Liquid\Performance\benchmarks\Support\ComplexThemeFixture;

test('complex theme fixture renders the deterministic collection page', function () {
    $environment = ComplexThemeFixture::environment();

    $page = $environment->parseTemplate(ComplexThemeFixture::rootTemplateName());
    $layout = $environment->parseTemplate(ComplexThemeFixture::LAYOUT_TEMPLATE_NAME);

    $content = $page->render(ComplexThemeFixture::newRenderContext($environment));
    $rendered = $layout->render(ComplexThemeFixture::newLayoutRenderContext($environment, $content));

    $streamedContent = $page->stream(ComplexThemeFixture::newRenderContext($environment));
    $streamed = implode('', iterator_to_array(
        $layout->stream(ComplexThemeFixture::newLayoutRenderContext($environment, $streamedContent)),
        false,
    ));

    expect($rendered)
        ->toContain('<title>Northstar Goods &mdash; Summer Essentials</title>')
        ->toContain('24 products selected')
        ->toContain('data-handle="sketchbook"')
        ->and(hash('sha256', $rendered))->toBe('52a906d56a26957edbf34fc85ed8dc76e3ccb74f9aa012effd5fd84de5e732eb')
        ->and($streamed)->toBe($rendered);
});
