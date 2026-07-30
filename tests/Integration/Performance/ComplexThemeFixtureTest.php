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
            ->toContain($expectedContent)
            ->toContain('<footer class="site-footer">')
            ->and($streamed)->toBe($rendered);
    }
});
