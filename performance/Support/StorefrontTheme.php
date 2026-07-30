<?php

namespace Keepsuit\Liquid\Performance\Support;

use Keepsuit\Liquid\Environment;
use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\FileSystems\LocalFileSystem;
use Keepsuit\Liquid\Render\RenderContext;

/**
 * Entry point for the storefront benchmark theme.
 *
 * Benchmarks build the environment with the library defaults; the fixture test
 * layers strictVariables, strictFilters and rethrowErrors on top of
 * environmentFactory(), so a missing variable, a missing filter or a swallowed
 * render error fails the suite instead of rendering as empty output.
 */
final class StorefrontTheme
{
    public const LAYOUT_TEMPLATE_NAME = 'layout.theme';

    public const ROOT_TEMPLATE_NAME = 'templates.collection';

    /**
     * @var list<string>
     */
    private const PAGE_TEMPLATE_NAMES = [
        'templates.index',
        self::ROOT_TEMPLATE_NAME,
        'templates.product',
        'templates.page',
    ];

    /**
     * @var list<string>
     */
    private const SNIPPET_TEMPLATE_NAMES = [
        'snippets.shared.site_header',
        'snippets.shared.site_footer',
        'snippets.shared.meta_tags',
        'snippets.shared.breadcrumbs',
        'snippets.shared.button',
        'snippets.shared.icon',
        'snippets.shared.price',
        'snippets.shared.newsletter',
        'snippets.collection.header',
        'snippets.collection.toolbar',
        'snippets.collection.filters',
        'snippets.collection.grid',
        'snippets.index.hero',
        'snippets.index.featured_products',
        'snippets.index.journal',
        'snippets.product.card',
        'snippets.product.gallery',
        'snippets.product.pricing',
        'snippets.product.badges',
        'snippets.product.variant_picker',
        'snippets.product.specs',
        'snippets.product.detail',
        'snippets.page.header',
        'snippets.page.content',
    ];

    public static function themePath(): string
    {
        return dirname(__DIR__).'/themes/storefront';
    }

    public static function environmentFactory(): EnvironmentFactory
    {
        return EnvironmentFactory::new()
            ->setFilesystem(new LocalFileSystem(self::themePath()))
            ->registerFilters(StorefrontFilters::class);
    }

    public static function environment(): Environment
    {
        return self::environmentFactory()->build();
    }

    /**
     * Every template the theme owns: the layout, the page templates and every
     * snippet. Parsing benchmarks walk this list.
     *
     * @return list<string>
     */
    public static function templateNames(): array
    {
        return [
            self::LAYOUT_TEMPLATE_NAME,
            ...self::PAGE_TEMPLATE_NAMES,
            ...self::SNIPPET_TEMPLATE_NAMES,
        ];
    }

    /**
     * @return list<string>
     */
    public static function pageTemplateNames(): array
    {
        return self::PAGE_TEMPLATE_NAMES;
    }

    public static function templatePath(string $templateName): string
    {
        return self::themePath().'/'.str_replace('.', '/', $templateName).'.liquid';
    }

    public static function templateSource(string $templateName): string
    {
        $source = file_get_contents(self::templatePath($templateName));

        if ($source === false) {
            throw new \RuntimeException("Could not read fixture template [$templateName].");
        }

        return $source;
    }

    /**
     * @return array<string, mixed>
     */
    public static function renderData(string $pageTemplateName = self::ROOT_TEMPLATE_NAME): array
    {
        $template = str_replace('templates.', '', $pageTemplateName);

        return [
            'shop' => Database::shop(),
            'cart' => Database::cart(),
            'linklists' => [
                'main_menu' => Database::mainMenu(),
                'footer' => Database::footerMenu(),
            ],
            'template' => $template,
            'page_title' => match ($template) {
                'index' => 'New arrivals',
                'product' => 'Weekend Bag',
                'page' => 'Our story',
                default => 'Summer Essentials',
            },
            'collection' => Database::collection(),
            'product' => Database::product(),
            'page' => Database::page(),
            'articles' => Database::articles(),
            'products_per_page' => Database::PRODUCTS_PER_PAGE,
        ];
    }

    public static function newRenderContext(
        Environment $environment,
        string $pageTemplateName = self::ROOT_TEMPLATE_NAME,
    ): RenderContext {
        return $environment->newRenderContext(staticData: self::renderData($pageTemplateName));
    }

    public static function newLayoutRenderContext(
        Environment $environment,
        mixed $content,
        string $pageTemplateName = self::ROOT_TEMPLATE_NAME,
    ): RenderContext {
        return $environment->newRenderContext(staticData: [
            ...self::renderData($pageTemplateName),
            'content_for_layout' => $content,
        ]);
    }

    /**
     * The page renders into its own context, then the layout renders into a second
     * one. That means the layout cannot see variables the page assigned — nothing
     * under layout/ may read template-assigned state.
     */
    public static function renderPage(Environment $environment, string $pageTemplateName): string
    {
        $content = $environment->parseTemplate($pageTemplateName)
            ->render(self::newRenderContext($environment, $pageTemplateName));

        return $environment->parseTemplate(self::LAYOUT_TEMPLATE_NAME)
            ->render(self::newLayoutRenderContext($environment, $content, $pageTemplateName));
    }

    /**
     * @return \Generator<string>
     */
    public static function streamPage(Environment $environment, string $pageTemplateName): \Generator
    {
        $content = $environment->parseTemplate($pageTemplateName)
            ->stream(self::newRenderContext($environment, $pageTemplateName));

        return $environment->parseTemplate(self::LAYOUT_TEMPLATE_NAME)
            ->stream(self::newLayoutRenderContext($environment, $content, $pageTemplateName));
    }
}
