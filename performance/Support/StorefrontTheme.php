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
    /** @var list<string>|null */
    private static ?array $templateNames = null;

    /** @var list<string>|null */
    private static ?array $pageTemplateNames = null;

    private static ?string $layoutTemplateName = null;

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
        return self::$templateNames ??= self::discoverTemplateNames();
    }

    /**
     * @return list<string>
     */
    public static function pageTemplateNames(): array
    {
        return self::$pageTemplateNames ??= array_values(array_filter(
            self::templateNames(),
            static fn (string $templateName): bool => str_starts_with($templateName, 'templates.'),
        ));
    }

    public static function layoutTemplateName(): string
    {
        if (self::$layoutTemplateName !== null) {
            return self::$layoutTemplateName;
        }

        $layouts = array_values(array_filter(
            self::templateNames(),
            static fn (string $templateName): bool => str_starts_with($templateName, 'layout.'),
        ));

        if (count($layouts) !== 1) {
            throw new \RuntimeException('The storefront theme must contain exactly one layout template.');
        }

        return self::$layoutTemplateName = $layouts[0];
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
     * @return array{page: array<string, mixed>, layout: array<string, mixed>}
     */
    public static function renderData(string $pageTemplateName): array
    {
        $template = str_replace('templates.', '', $pageTemplateName);
        $shop = Database::shop();

        return [
            'page' => Database::pageData($template, $shop),
            'layout' => [
                'shop' => $shop,
                'cart' => Database::cart(),
                'linklists' => [
                    'main_menu' => Database::mainMenu(),
                    'footer' => Database::footerMenu(),
                ],
                'template' => $template,
                'page_title' => Database::pageTitle($template),
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private static function newRenderContext(
        Environment $environment,
        array $data,
    ): RenderContext {
        return $environment->newRenderContext(staticData: $data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private static function newLayoutRenderContext(
        Environment $environment,
        mixed $content,
        array $data,
    ): RenderContext {
        return $environment->newRenderContext(staticData: [
            ...$data,
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
        $data = self::renderData($pageTemplateName);
        $content = $environment->parseTemplate($pageTemplateName)
            ->render(self::newRenderContext($environment, $data['page']));

        return $environment->parseTemplate(self::layoutTemplateName())
            ->render(self::newLayoutRenderContext($environment, $content, $data['layout']));
    }

    /**
     * @return \Generator<string>
     */
    public static function streamPage(Environment $environment, string $pageTemplateName): \Generator
    {
        $data = self::renderData($pageTemplateName);
        $content = $environment->parseTemplate($pageTemplateName)
            ->stream(self::newRenderContext($environment, $data['page']));

        return $environment->parseTemplate(self::layoutTemplateName())
            ->stream(self::newLayoutRenderContext($environment, $content, $data['layout']));
    }

    /**
     * @return list<string>
     */
    private static function discoverTemplateNames(): array
    {
        $templateNames = [];
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(
            self::themePath(),
            \FilesystemIterator::SKIP_DOTS,
        ));

        foreach ($files as $file) {
            if (! $file instanceof \SplFileInfo || ! $file->isFile() || $file->getExtension() !== 'liquid') {
                continue;
            }

            $relativePath = substr($file->getPathname(), strlen(self::themePath()) + 1, -7);
            $templateNames[] = str_replace(DIRECTORY_SEPARATOR, '.', $relativePath);
        }

        sort($templateNames);

        if ($templateNames === []) {
            throw new \RuntimeException('The storefront theme contains no Liquid templates.');
        }

        return $templateNames;
    }
}
