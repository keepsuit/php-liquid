<?php

namespace Keepsuit\Liquid\Performance\benchmarks;

use Keepsuit\Liquid\Environment;
use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\FileSystems\LocalFileSystem;
use Keepsuit\Liquid\Performance\Shopify\CommentFormTag;
use Keepsuit\Liquid\Performance\Shopify\CustomFilters;
use Keepsuit\Liquid\Performance\Shopify\Database;
use Keepsuit\Liquid\Performance\Shopify\PaginateTag;
use Keepsuit\Liquid\Support\Arr;
use Keepsuit\Liquid\TemplatesCache\MemoryTemplatesCache;
use Keepsuit\Liquid\TemplatesCache\SerializeTemplatesCache;
use Keepsuit\Liquid\TemplatesCache\VarExportTemplatesCache;
use PhpBench\Attributes\AfterMethods;
use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\Iterations;
use PhpBench\Attributes\OutputMode;
use PhpBench\Attributes\OutputTimeUnit;
use PhpBench\Attributes\Revs;
use PhpBench\Attributes\Warmup;

#[Iterations(10)]
#[Revs(10)]
#[Warmup(1)]
#[OutputMode('throughput')]
#[OutputTimeUnit('seconds', precision: 3)]
#[AfterMethods('clearCache')]
class TemplateCacheBench
{
    protected Environment $environment;

    protected array $templates;

    #[BeforeMethods('setupInMemory')]
    public function benchInMemory(): void
    {
        $this->renderTemplates();
    }

    public function setupInMemory(): void
    {
        $this->environment = $this->environmentBuilder()
            ->setTemplatesCache(new MemoryTemplatesCache)
            ->build();

        $this->loadTemplates();
    }

    #[BeforeMethods('setupVarExporter')]
    public function benchVarExporter(): void
    {
        $this->renderTemplates();
    }

    public function setupVarExporter(): void
    {
        $this->environment = $this->environmentBuilder()
            ->setTemplatesCache(new VarExportTemplatesCache(__DIR__.'/cache/var_export'))
            ->build();

        $this->loadTemplates();
    }

    #[BeforeMethods('setupSerialize')]
    public function benchSerialize(): void
    {
        $this->renderTemplates();
    }

    public function setupSerialize(): void
    {
        $this->environment = $this->environmentBuilder()
            ->setTemplatesCache(new SerializeTemplatesCache(__DIR__.'/cache/serialize'))
            ->build();

        $this->loadTemplates();
    }

    protected function loadTemplates(): void
    {
        $baseDir = __DIR__.'/../tests';
        $files = glob($baseDir.'/**/*.liquid');

        if ($files === false) {
            throw new \RuntimeException('Could not find any tests');
        }

        $this->templates = Arr::map($files, function (string $path) use ($baseDir) {
            // relative path to the base directory
            $name = str_replace($baseDir.'/', '', $path);
            // remove .liquid extension
            $name = substr($name, 0, -7);

            // replace / with .
            return str_replace('/', '.', $name);
        });

        foreach ($this->templates as $template) {
            $this->environment->parseTemplate($template);
        }
    }

    protected function environmentBuilder(): EnvironmentFactory
    {
        return EnvironmentFactory::new()
            ->setFilesystem(new LocalFileSystem(__DIR__.'/../tests'))
            ->registerTag(CommentFormTag::class)
            ->registerTag(PaginateTag::class)
            ->registerFilters(CustomFilters::class);
    }

    protected function renderTemplates(): void
    {
        foreach ($this->templates as $template) {
            $this->environment->parseTemplate($template)
                ->render($this->environment->newRenderContext(staticData: [...Database::tables()]));
        }
    }

    public function clearCache(): void
    {
        $this->environment->templatesCache->clear();
    }
}
