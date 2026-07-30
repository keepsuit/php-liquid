<?php

namespace Keepsuit\Liquid\Performance\benchmarks\Support;

use Keepsuit\Liquid\Attributes\Cache;
use Keepsuit\Liquid\Contracts\LiquidFileSystem;
use Keepsuit\Liquid\Contracts\LiquidTemplatesCache;
use Keepsuit\Liquid\Drop;
use Keepsuit\Liquid\Environment;
use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\Filters\FiltersProvider;
use Keepsuit\Liquid\Render\RenderContext;

final class ComplexThemeFixture
{
    public const ROOT_TEMPLATE_NAME = 'collection-page';

    public static function environment(?LiquidTemplatesCache $templatesCache = null): Environment
    {
        $factory = EnvironmentFactory::new()
            ->setFilesystem(new ComplexThemeFileSystem(self::templateSources()))
            ->registerFilters(ComplexThemeFilters::class);

        if ($templatesCache !== null) {
            $factory->setTemplatesCache($templatesCache);
        }

        return $factory->build();
    }

    public static function rootTemplateName(): string
    {
        return self::ROOT_TEMPLATE_NAME;
    }

    public static function rootTemplateSource(): string
    {
        return self::templateSources()[self::ROOT_TEMPLATE_NAME];
    }

    /**
     * @return array<string, string>
     */
    public static function templateSources(): array
    {
        return [
            self::ROOT_TEMPLATE_NAME => '{% render "page-shell", collection: collection %}',
            'page-shell' => "<main data-collection=\"{{ collection.handle }}\">\n  {% render \"collection-section\", collection: collection %}\n</main>",
            'collection-section' => "<header><h1>{{ collection.title }}</h1><p>{{ collection.products | size }} products</p></header>\n  <section class=\"products\">\n{% for product in collection.products %}    {% render \"product-card\", product: product, label: collection.label %}\n{% endfor %}  </section>",
            'product-card' => '<article data-handle="{{ product.handle }}"><h2>{{ product.title }}</h2><p class="vendor">{{ product.vendor | upcase }}</p><p class="price">{{ product.price_cents | fixture_money }}</p><p class="label">{{ label }}</p><p class="summary">{{ product.title }} / {{ product.vendor }} / {{ product.inventory_label }} | {{ product.title }} / {{ product.vendor }} / {{ product.inventory_label }}</p></article>',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function renderData(): array
    {
        return [
            'collection' => [
                'handle' => 'summer-essentials',
                'title' => 'Summer Essentials',
                'label' => 'summer',
                'products' => [
                    new ComplexThemeProduct('linen-shirt', 'Linen Shirt', 'ACME Apparel', 3950),
                    new ComplexThemeProduct('canvas-tote', 'Canvas Tote', 'Field Goods', 2400),
                    new ComplexThemeProduct('sun-hat', 'Sun Hat', 'Coastline', 1875),
                ],
            ],
        ];
    }

    public static function newRenderContext(Environment $environment): RenderContext
    {
        return $environment->newRenderContext(staticData: self::renderData());
    }
}

final class ComplexThemeFileSystem implements LiquidFileSystem
{
    /**
     * @param  array<string, string>  $templates
     */
    public function __construct(
        private readonly array $templates,
    ) {}

    public function readTemplateFile(string $templateName): string
    {
        return $this->templates[$templateName] ?? throw new \RuntimeException("Unknown fixture template [$templateName].");
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
    public function __construct(
        public readonly string $handle,
        public readonly string $title,
        private readonly string $vendorName,
        private readonly int $priceCents,
    ) {}

    public function vendor(): string
    {
        return $this->vendorName;
    }

    public function priceCents(): int
    {
        return $this->priceCents;
    }

    #[Cache]
    public function inventoryLabel(): int
    {
        return $this->priceCents;
    }
}
