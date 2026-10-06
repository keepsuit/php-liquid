<?php

namespace Keepsuit\Liquid\Performance\benchmarks;

use Keepsuit\Liquid\Contracts\LiquidTemplatesCache;
use Keepsuit\Liquid\Environment;
use Keepsuit\Liquid\Performance\Support\StorefrontTheme;
use Keepsuit\Liquid\TemplatesCache\CompiledTemplatesCache;
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
use Spatie\TemporaryDirectory\TemporaryDirectory;

/**
 * Every backend has the same two subjects:
 * - Build: parse the whole theme into an empty cache.
 * - LoadAndRender: a fresh environment per revolution, as in a new request,
 *   loads the warm cache and renders every page.
 */
#[Groups(['cache'])]
#[Iterations(10)]
#[Revs(20)]
#[OutputMode('throughput')]
#[OutputTimeUnit('seconds', precision: 3)]
#[AfterMethods('tearDown')]
class TemplateCacheBench
{
    private string $backend;

    private TemporaryDirectory $cacheDirectory;

    private LiquidTemplatesCache $cache;

    private Environment $environment;

    #[BeforeMethods('setUpInMemory')]
    public function benchBuildInMemory(): void
    {
        $this->build();
    }

    #[BeforeMethods('setUpInMemoryWarm')]
    public function benchLoadAndRenderInMemory(): void
    {
        $this->loadAndRender();
    }

    #[BeforeMethods('setUpSerialize')]
    public function benchBuildSerialize(): void
    {
        $this->build();
    }

    #[BeforeMethods('setUpSerializeWarm')]
    public function benchLoadAndRenderSerialize(): void
    {
        $this->loadAndRender();
    }

    #[BeforeMethods('setUpVarExporter')]
    public function benchBuildVarExporter(): void
    {
        $this->build();
    }

    #[BeforeMethods('setUpVarExporterWarm')]
    public function benchLoadAndRenderVarExporter(): void
    {
        $this->loadAndRender();
    }

    #[BeforeMethods('setUpCompiled')]
    public function benchBuildCompiled(): void
    {
        $this->build();
    }

    #[BeforeMethods('setUpCompiledWarm')]
    public function benchLoadAndRenderCompiled(): void
    {
        $this->loadAndRender();
    }

    public function setUpInMemory(): void
    {
        $this->setUp('memory');
    }

    public function setUpInMemoryWarm(): void
    {
        $this->setUp('memory', warm: true);
    }

    public function setUpSerialize(): void
    {
        $this->setUp('serialize');
    }

    public function setUpSerializeWarm(): void
    {
        $this->setUp('serialize', warm: true);
    }

    public function setUpVarExporter(): void
    {
        $this->setUp('var-exporter');
    }

    public function setUpVarExporterWarm(): void
    {
        $this->setUp('var-exporter', warm: true);
    }

    public function setUpCompiled(): void
    {
        $this->setUp('compiled');
    }

    public function setUpCompiledWarm(): void
    {
        $this->setUp('compiled', warm: true);
    }

    public function tearDown(): void
    {
        $this->cache->clear();

        $this->cacheDirectory->delete();
    }

    private function setUp(string $backend, bool $warm = false): void
    {
        $this->backend = $backend;
        $this->cacheDirectory = (new TemporaryDirectory)->deleteWhenDestroyed()->create();
        $this->cache = $this->newCache(keepInMemory: false);
        $this->environment = $this->newEnvironment($this->cache);

        if ($warm) {
            $this->build();
        }
    }

    private function build(): void
    {
        $this->cache->clear();

        foreach (StorefrontTheme::templateNames() as $templateName) {
            $this->environment->parseTemplate($templateName);
        }
    }

    private function loadAndRender(): void
    {
        $environment = $this->newEnvironment($this->backend === 'memory' ? $this->cache : $this->newCache());

        foreach (StorefrontTheme::pageTemplateNames() as $templateName) {
            StorefrontTheme::renderPage($environment, $templateName);
        }
    }

    private function newEnvironment(LiquidTemplatesCache $cache): Environment
    {
        return StorefrontTheme::environmentFactory()->setTemplatesCache($cache)->build();
    }

    private function newCache(bool $keepInMemory = true): LiquidTemplatesCache
    {
        return match ($this->backend) {
            'memory' => new MemoryTemplatesCache,
            'serialize' => new SerializeTemplatesCache($this->cacheDirectory->path(), $keepInMemory),
            'var-exporter' => new VarExportTemplatesCache($this->cacheDirectory->path(), $keepInMemory),
            'compiled' => new CompiledTemplatesCache($this->cacheDirectory->path(), $keepInMemory),
            default => throw new \InvalidArgumentException("Unknown templates cache backend [{$this->backend}]."),
        };
    }
}
