<?php

use Keepsuit\Liquid\Performance\benchmarks\Support\ComplexThemeFixture;

test('complex theme fixture renders the deterministic collection page', function () {
    $environment = ComplexThemeFixture::environment();

    $template = $environment->parseString(
        ComplexThemeFixture::rootTemplateSource(),
        ComplexThemeFixture::rootTemplateName(),
    );

    expect($template->render(ComplexThemeFixture::newRenderContext($environment)))->toBe(<<<'HTML'
<main data-collection="summer-essentials">
  <header><h1>Summer Essentials</h1><p>3 products</p></header>
  <section class="products">
    <article data-handle="linen-shirt"><h2>Linen Shirt</h2><p class="vendor">ACME APPAREL</p><p class="price">€39.50</p><p class="label">summer</p><p class="summary">Linen Shirt / ACME Apparel / 3950 | Linen Shirt / ACME Apparel / 3950</p></article>
    <article data-handle="canvas-tote"><h2>Canvas Tote</h2><p class="vendor">FIELD GOODS</p><p class="price">€24.00</p><p class="label">summer</p><p class="summary">Canvas Tote / Field Goods / 2400 | Canvas Tote / Field Goods / 2400</p></article>
    <article data-handle="sun-hat"><h2>Sun Hat</h2><p class="vendor">COASTLINE</p><p class="price">€18.75</p><p class="label">summer</p><p class="summary">Sun Hat / Coastline / 1875 | Sun Hat / Coastline / 1875</p></article>
  </section>
</main>
HTML);
});
