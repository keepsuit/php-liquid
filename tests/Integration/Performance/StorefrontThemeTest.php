<?php

use Keepsuit\Liquid\Performance\Support\Database;
use Keepsuit\Liquid\Performance\Support\StorefrontTheme;

/**
 * The benchmark environment runs with the library defaults, because that is what
 * a real application looks like. The fixture is verified with every strict option
 * on instead: strictVariables and strictFilters turn a missing drop method,
 * variable or filter into a failure here, rather than into empty output nobody
 * notices in a benchmark.
 */
function storefrontStrictEnvironment(): Keepsuit\Liquid\Environment
{
    return StorefrontTheme::environmentFactory()
        ->setStrictVariables(true)
        ->setStrictFilters(true)
        ->setRethrowErrors(true)
        ->build();
}

test('every discovered template exists and every template on disk is discovered', function () {
    $discovered = StorefrontTheme::templateNames();

    expect($discovered)->toHaveCount(29);

    foreach ($discovered as $templateName) {
        expect(StorefrontTheme::templatePath($templateName))->toBeReadableFile();
    }

    $onDisk = [];
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(StorefrontTheme::themePath()));
    foreach ($files as $file) {
        if (! $file instanceof SplFileInfo) {
            continue;
        }

        if ($file->isFile() && $file->getExtension() === 'liquid') {
            $relative = substr($file->getPathname(), strlen(StorefrontTheme::themePath()) + 1, -7);
            $onDisk[] = str_replace(DIRECTORY_SEPARATOR, '.', $relative);
        }
    }

    sort($onDisk);
    $expected = $discovered;
    sort($expected);

    // An undiscovered template is invisible to the parse benchmarks, so it would be
    // silently excluded from every measurement.
    expect($onDisk)->toBe($expected);
});

test('every page renders through the layout with no missing variables or filters', function () {
    $environment = storefrontStrictEnvironment();

    foreach ([
        'templates.index' => 'Better everyday rituals',
        'templates.collection' => 'Summer Essentials',
        'templates.product' => 'Weekend Bag',
        'templates.page' => 'Our story',
    ] as $templateName => $expectedContent) {
        $rendered = StorefrontTheme::renderPage($environment, $templateName);

        expect($rendered)
            ->toContain('<header class="site-header">')
            ->toContain($expectedContent)
            ->toContain('<footer class="site-footer">')
            // Fixture filters, asserted on their output: an unknown filter renders
            // its input unchanged, so only a filtered value proves it ran.
            ->toContain('3 items &middot; €126.50')
            // A capture that escapes its assembled result would double-escape this.
            ->toMatch('/<title>[^<]+ &mdash; Northstar Goods<\/title>/');
    }
});

test('page and layout data stay scoped to their own template work', function () {
    $indexData = StorefrontTheme::renderData('templates.index');
    $productData = StorefrontTheme::renderData('templates.product');

    expect($indexData['page'])->toHaveKeys(['shop', 'collection', 'articles'])
        ->not->toHaveKeys(['cart', 'linklists', 'product', 'page'])
        ->and($productData['page'])->toHaveKeys(['collection', 'product'])
        ->not->toHaveKeys(['shop', 'cart', 'linklists', 'articles', 'page'])
        ->and($indexData['layout'])->toHaveKeys(['shop', 'cart', 'linklists', 'template', 'page_title'])
        ->and($indexData['layout']['shop'])->toBe($indexData['page']['shop']);
});

test('every page keeps its page-specific partial workload', function () {
    $environment = storefrontStrictEnvironment();

    foreach ([
        'templates.index' => ['class="hero"', 'Packing light for a weekend away'],
        'templates.collection' => ['class="collection-filters"', 'class="product-grid"'],
        'templates.product' => ['class="variant-picker"', '<caption>Details</caption>'],
        'templates.page' => ['class="page-content__section"', 'Contact us'],
    ] as $templateName => $markers) {
        $rendered = StorefrontTheme::renderPage($environment, $templateName);

        expect($rendered)
            ->toContain($markers[0])
            ->toContain($markers[1]);
    }
});

test('streaming a page produces the same bytes as rendering it', function () {
    $environment = storefrontStrictEnvironment();

    foreach (StorefrontTheme::pageTemplateNames() as $templateName) {
        $rendered = StorefrontTheme::renderPage($environment, $templateName);
        $streamed = implode('', iterator_to_array(
            StorefrontTheme::streamPage($environment, $templateName),
            false,
        ));

        expect($streamed)->toBe($rendered);
    }
});

test('the collection page renders every product in the collection', function () {
    $environment = storefrontStrictEnvironment();

    $rendered = StorefrontTheme::renderPage($environment, 'templates.collection');

    expect(Database::collection()->products)->toHaveCount(Database::PRODUCTS_PER_PAGE)
        ->and(substr_count($rendered, 'class="product-card"'))->toBe(Database::PRODUCTS_PER_PAGE);
});

test('hand-wired cart totals stay consistent with the rendered summary', function () {
    // Database assigns rather than computes, so the cart total is a literal that
    // can drift from item_count. Pin both rather than trusting the data.
    $cart = Database::cart();

    expect($cart->itemCount)->toBe(3)
        ->and($cart->totalPrice)->toBe(12650)
        ->and($cart->isEmpty())->toBeFalse();
});

test('products expose each drop resolution strategy the theme relies on', function () {
    $product = Database::product();

    // Public typed property.
    expect($product->title)->toBe('Weekend Bag');

    // Derived methods.
    expect($product->onSale())->toBeTrue();
    expect($product->savingCents())->toBe(2000);

    // #[Cache]d method, and its value must survive a second read.
    expect($product->inStockVariantCount())->toBe(3);
    expect($product->inStockVariantCount())->toBe(3);
});

test('metafields resolve through liquidMethodMissing when the theme renders them', function () {
    // The one dynamic path in the fixture, asserted through a real render so the
    // template lookup is what proves it, not a direct property access.
    $rendered = StorefrontTheme::renderPage(storefrontStrictEnvironment(), 'templates.product');

    expect($rendered)
        ->toContain('<td>Waxed canvas, bridle leather</td>')
        ->toContain('<td>Condition leather twice a year</td>');
});
