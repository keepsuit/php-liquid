<?php

use Keepsuit\Liquid\Performance\benchmarks\Support\ComplexThemeFixture;

test('complex theme fixture renders every page through the shared layout', function () {
    $environment = ComplexThemeFixture::environment();

    expect(ComplexThemeFixture::templateNames())
        ->toContain('layout.theme')
        ->toContain('templates.index')
        ->toContain('templates.collection')
        ->toContain('templates.product')
        ->toContain('templates.page')
        ->toContain('snippets.shared.button')
        ->toContain('snippets.product.card');

    foreach ([
        'templates.index' => 'Better everyday rituals',
        'templates.collection' => '24 products selected',
        'templates.product' => 'Weekend Bag',
        'templates.page' => 'Northstar journal',
    ] as $templateName => $expectedContent) {
        $rendered = ComplexThemeFixture::renderPage($environment, $templateName);
        $streamed = implode('', iterator_to_array(
            ComplexThemeFixture::streamPage($environment, $templateName),
            false,
        ));

        expect($rendered)
            ->toContain('<header class="site-header">')
            // Unknown filters render as their unmodified input, so assert on
            // filtered output to keep a missing filter from passing silently.
            ->toContain('Cart: 3 items / €126.50')
            ->toContain($expectedContent)
            ->toContain('<footer class="site-footer">')
            ->and($streamed)->toBe($rendered);
    }
});
