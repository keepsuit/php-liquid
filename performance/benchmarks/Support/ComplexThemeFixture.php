<?php

namespace Keepsuit\Liquid\Performance\benchmarks\Support;

use Keepsuit\Liquid\Attributes\Cache;
use Keepsuit\Liquid\Contracts\LiquidTemplatesCache;
use Keepsuit\Liquid\Drop;
use Keepsuit\Liquid\Environment;
use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\FileSystems\LocalFileSystem;
use Keepsuit\Liquid\Filters\FiltersProvider;
use Keepsuit\Liquid\Render\RenderContext;

final class ComplexThemeFixture
{
    public const ROOT_TEMPLATE_NAME = 'collection';

    public const LAYOUT_TEMPLATE_NAME = 'theme';

    private const PRODUCTS = [
        ['linen-shirt', 'Linen Shirt', 'ACME Apparel', 3950, 5900, ['new', 'linen']],
        ['canvas-tote', 'Canvas Tote', 'Field Goods', 2400, 3200, ['travel', 'sale']],
        ['sun-hat', 'Sun Hat', 'Coastline', 1875, 2500, ['summer', 'sale']],
        ['ceramic-mug', 'Ceramic Mug', 'Studio Form', 2200, 2200, ['kitchen', 'new']],
        ['weekend-bag', 'Weekend Bag', 'Field Goods', 8900, 10900, ['travel', 'limited']],
        ['cotton-throw', 'Cotton Throw', 'North Loom', 7600, 7600, ['home', 'soft']],
        ['leather-wallet', 'Leather Wallet', 'Atelier No. 8', 5200, 6500, ['gift', 'sale']],
        ['travel-bottle', 'Travel Bottle', 'Coastline', 2800, 2800, ['travel', 'summer']],
        ['desk-lamp', 'Desk Lamp', 'Studio Form', 6800, 8100, ['home', 'new']],
        ['wool-socks', 'Wool Socks', 'North Loom', 1600, 1600, ['warm', 'gift']],
        ['market-basket', 'Market Basket', 'Field Goods', 4300, 5000, ['home', 'limited']],
        ['notebook-set', 'Notebook Set', 'Paper Mill', 1900, 1900, ['desk', 'new']],
        ['silk-scarf', 'Silk Scarf', 'Atelier No. 8', 7400, 8900, ['gift', 'sale']],
        ['beach-towel', 'Beach Towel', 'Coastline', 3600, 3600, ['summer', 'travel']],
        ['glass-vase', 'Glass Vase', 'Studio Form', 4600, 5200, ['home', 'limited']],
        ['knit-cap', 'Knit Cap', 'North Loom', 3100, 3100, ['warm', 'new']],
        ['key-organizer', 'Key Organizer', 'Atelier No. 8', 4100, 4800, ['gift', 'travel']],
        ['picnic-blanket', 'Picnic Blanket', 'Field Goods', 9200, 11200, ['summer', 'home']],
        ['tea-canister', 'Tea Canister', 'Paper Mill', 2700, 2700, ['kitchen', 'desk']],
        ['camp-lantern', 'Camp Lantern', 'Coastline', 5400, 6200, ['travel', 'limited']],
        ['table-clock', 'Table Clock', 'Studio Form', 5700, 5700, ['home', 'desk']],
        ['cashmere-wrap', 'Cashmere Wrap', 'North Loom', 12800, 14900, ['warm', 'gift']],
        ['card-holder', 'Card Holder', 'Atelier No. 8', 3300, 3300, ['gift', 'new']],
        ['sketchbook', 'Sketchbook', 'Paper Mill', 2500, 2500, ['desk', 'travel']],
    ];

    public static function environment(?LiquidTemplatesCache $templatesCache = null): Environment
    {
        $factory = EnvironmentFactory::new()
            ->setFilesystem(new LocalFileSystem(self::themePath()))
            ->registerFilters(ComplexThemeFilters::class);

        if ($templatesCache !== null) {
            $factory->setTemplatesCache($templatesCache);
        }

        return $factory->build();
    }

    public static function themePath(): string
    {
        return dirname(__DIR__, 2).'/themes/complex-collection';
    }

    public static function rootTemplateName(): string
    {
        return self::ROOT_TEMPLATE_NAME;
    }

    /**
     * @return list<string>
     */
    public static function templateNames(): array
    {
        return [
            self::ROOT_TEMPLATE_NAME,
            self::LAYOUT_TEMPLATE_NAME,
            'collection_header',
            'collection_navigation',
            'collection_grid',
            'product_card',
            'product_media',
            'product_pricing',
            'product_metadata',
        ];
    }

    public static function templateSource(string $templateName): string
    {
        $source = file_get_contents(self::themePath().'/'.$templateName.'.liquid');

        if ($source === false) {
            throw new \RuntimeException("Could not read fixture template [$templateName].");
        }

        return $source;
    }

    /**
     * @return array<string, mixed>
     */
    public static function renderData(): array
    {
        return [
            'shop' => [
                'name' => 'Northstar Goods',
                'currency' => 'EUR',
            ],
            'cart' => [
                'item_count' => 3,
                'total_price' => 12650,
            ],
            'linklists' => [
                'main-menu' => [
                    'links' => [
                        ['url' => '/collections/summer-essentials', 'title' => 'Summer'],
                        ['url' => '/collections/travel', 'title' => 'Travel'],
                        ['url' => '/collections/home', 'title' => 'Home'],
                    ],
                ],
                'footer' => [
                    'links' => [
                        ['url' => '/pages/shipping', 'title' => 'Shipping'],
                        ['url' => '/pages/returns', 'title' => 'Returns'],
                        ['url' => '/pages/contact', 'title' => 'Contact'],
                    ],
                ],
            ],
            'page_title' => 'Summer Essentials',
            'template' => 'collection',
            'collection' => [
                'handle' => 'summer-essentials',
                'title' => 'Summer Essentials',
                'label' => 'summer',
                'description' => 'Everyday pieces for long weekends, slow mornings, and bright afternoons.',
                'tags' => ['New arrivals', 'Travel ready', 'Summer layers', 'Gifts under €100'],
                'products' => array_map(
                    static fn (array $product) => new ComplexThemeProduct(...$product),
                    self::PRODUCTS,
                ),
            ],
        ];
    }

    public static function newRenderContext(Environment $environment): RenderContext
    {
        return $environment->newRenderContext(staticData: self::renderData());
    }

    public static function newLayoutRenderContext(Environment $environment, mixed $content): RenderContext
    {
        return $environment->newRenderContext(staticData: [
            ...self::renderData(),
            'content_for_layout' => $content,
        ]);
    }
}

final class ComplexThemeFilters extends FiltersProvider
{
    public function fixtureMoney(int|float $cents): string
    {
        return '€'.number_format($cents / 100, 2, '.', '');
    }
}

final class ComplexThemeProduct extends Drop
{
    /**
     * @param  list<string>  $badges
     */
    public function __construct(
        public readonly string $handle,
        public readonly string $title,
        private readonly string $vendorName,
        private readonly int $priceCents,
        private readonly int $compareAtPriceCents,
        public readonly array $badges,
    ) {}

    public function vendor(): string
    {
        return $this->vendorName;
    }

    public function priceCents(): int
    {
        return $this->priceCents;
    }

    public function compareAtPriceCents(): int
    {
        return $this->compareAtPriceCents;
    }

    #[Cache]
    public function inventoryLabel(): string
    {
        return $this->priceCents < 3000 ? 'Low stock' : 'In stock';
    }
}
