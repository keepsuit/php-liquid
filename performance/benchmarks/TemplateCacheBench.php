<?php

namespace Keepsuit\Liquid\Performance\benchmarks;

use Keepsuit\Liquid\Contracts\LiquidTemplatesCache;
use Keepsuit\Liquid\Environment;
use Keepsuit\Liquid\Performance\Support\CompiledTemplatesCache;
use Keepsuit\Liquid\Performance\Support\StorefrontTheme;
use Keepsuit\Liquid\TemplatesCache\MemoryTemplatesCache;
use Keepsuit\Liquid\TemplatesCache\SerializeTemplatesCache;
use Keepsuit\Liquid\TemplatesCache\VarExportTemplatesCache;
use PhpBench\Attributes\AfterMethods;
use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\Groups;
use PhpBench\Attributes\Iterations;
use PhpBench\Attributes\OutputMode;
use PhpBench\Attributes\OutputTimeUnit;
use PhpBench\Attributes\Revs;

#[Groups(['cache'])]
#[Iterations(10)]
#[Revs(20)]
#[OutputMode('throughput')]
#[OutputTimeUnit('seconds', precision: 3)]
#[AfterMethods('clearCache')]
class TemplateCacheBench
{
    private const CACHE_DIRECTORY = 'keepsuit-liquid-phpbench';

    private Environment $environment;

    private LiquidTemplatesCache $cache;

    /** @var list<string> */
    private array $templateNames;

    /** @var list<string> */
    private array $pageTemplateNames;

    private string $cacheDirectory;

    #[BeforeMethods('setUpInMemoryBuild')]
    public function benchBuildInMemory(): void
    {
        $this->buildStaticTheme();
    }

    #[BeforeMethods('setUpInMemoryCachedRender')]
    public function benchLoadAndRenderInMemory(): void
    {
        $this->renderCachedTheme();
    }

    #[BeforeMethods('setUpSerializeBuild')]
    public function benchBuildSerialize(): void
    {
        $this->buildStaticTheme();
    }

    #[BeforeMethods('setUpSerializeCachedRender')]
    public function benchLoadAndRenderSerialize(): void
    {
        $this->renderCachedTheme();
    }

    #[BeforeMethods('setUpVarExporterBuild')]
    public function benchBuildVarExporter(): void
    {
        $this->buildStaticTheme();
    }

    #[BeforeMethods('setUpVarExporterCachedRender')]
    public function benchLoadAndRenderVarExporter(): void
    {
        $this->renderCachedTheme();
    }

    #[BeforeMethods('setUpCompiledCachedRender')]
    public function benchLoadAndRenderCompiled(): void
    {
        $this->renderCachedTheme();
    }

    public function setUpInMemoryBuild(): void
    {
        $this->setUpBuild('memory');
    }

    public function setUpInMemoryCachedRender(): void
    {
        $this->setUpCachedRender('memory');
    }

    public function setUpSerializeBuild(): void
    {
        $this->setUpBuild('serialize');
    }

    public function setUpSerializeCachedRender(): void
    {
        $this->setUpCachedRender('serialize');
    }

    public function setUpVarExporterBuild(): void
    {
        $this->setUpBuild('var-exporter');
    }

    public function setUpVarExporterCachedRender(): void
    {
        $this->setUpCachedRender('var-exporter');
    }

    public function setUpCompiledCachedRender(): void
    {
        $this->templateNames = StorefrontTheme::templateNames();
        $this->pageTemplateNames = StorefrontTheme::pageTemplateNames();
        $this->cacheDirectory = sys_get_temp_dir().'/'.self::CACHE_DIRECTORY.'-'.bin2hex(random_bytes(8));
        $compiledCache = new CompiledTemplatesCache($this->cachePath('compiled'));
        $this->cache = $compiledCache;
        $compilerEnvironment = StorefrontTheme::environmentFactory()
            ->setTemplatesCache(new MemoryTemplatesCache)
            ->build();

        foreach ($this->templateNames as $templateName) {
            $template = $compilerEnvironment->parseTemplate($templateName);
            $compilerEnvironment->compile($template, $compiledCache->pathFor($templateName));
        }

        $this->environment = StorefrontTheme::environmentFactory()
            ->setTemplatesCache($this->cache)
            ->build();
    }

    public function clearCache(): void
    {
        $this->cache->clear();
    }

    private function setUpBuild(string $backend): void
    {
        $this->templateNames = StorefrontTheme::templateNames();
        $this->pageTemplateNames = StorefrontTheme::pageTemplateNames();
        $this->cacheDirectory = sys_get_temp_dir().'/'.self::CACHE_DIRECTORY.'-'.bin2hex(random_bytes(8));
        $this->cache = $this->newCache($backend);
        $this->cache->clear();
        $this->environment = StorefrontTheme::environmentFactory()->setTemplatesCache($this->cache)->build();
    }

    private function setUpCachedRender(string $backend): void
    {
        $this->setUpBuild($backend);
        $this->compileStaticTheme($this->environment);

        $this->cache = $backend === 'memory'
            ? $this->cache
            : $this->newCache($backend);
        $this->environment = StorefrontTheme::environmentFactory()->setTemplatesCache($this->cache)->build();
    }

    private function compileStaticTheme(Environment $environment): void
    {
        foreach ($this->templateNames as $templateName) {
            $environment->parseTemplate($templateName);
        }
    }

    private function buildStaticTheme(): void
    {
        $this->cache->clear();
        $this->compileStaticTheme($this->environment);
    }

    private function renderCachedTheme(): void
    {
        foreach ($this->pageTemplateNames as $templateName) {
            StorefrontTheme::renderPage($this->environment, $templateName);
        }
    }

    private function newCache(string $backend): LiquidTemplatesCache
    {
        return match ($backend) {
            'memory' => new MemoryTemplatesCache,
            'serialize' => new SerializeTemplatesCache($this->cachePath('serialize'), keepInMemory: false),
            'var-exporter' => new VarExportTemplatesCache($this->cachePath('var-exporter'), keepInMemory: false),
            default => throw new \InvalidArgumentException("Unknown templates cache backend [$backend]."),
        };
    }

    private function cachePath(string $backend): string
    {
        return $this->cacheDirectory.'/'.$backend;
    }
}
