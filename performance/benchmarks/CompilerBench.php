<?php

namespace Keepsuit\Liquid\Performance\benchmarks;

use Keepsuit\Liquid\Compiler\CompiledTemplateInterface;
use Keepsuit\Liquid\Environment;
use Keepsuit\Liquid\Performance\Support\StorefrontTheme;
use Keepsuit\Liquid\Template;
use Keepsuit\Liquid\TemplatesCache\MemoryTemplatesCache;
use PhpBench\Attributes\AfterMethods;
use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\Groups;
use PhpBench\Attributes\Iterations;
use PhpBench\Attributes\OutputMode;
use PhpBench\Attributes\OutputTimeUnit;
use PhpBench\Attributes\Revs;

/**
 * Measures the compiler pipeline against the production-shaped storefront fixture.
 *
 * Compilation and artifact loading are deliberately separate from render and
 * stream subjects. Fixture reads, parsing, compilation setup and data creation
 * all happen before a subject starts.
 */
#[Groups(['compiler'])]
#[Iterations(10)]
#[Revs(20)]
#[OutputMode('throughput')]
#[OutputTimeUnit('seconds', precision: 3)]
#[BeforeMethods('setUp')]
#[AfterMethods('tearDown')]
class CompilerBench
{
    private const DATA_SET_COUNT = 20;

    private Environment $interpretedEnvironment;

    private Environment $compiledEnvironment;

    private string $artifactDirectory;

    /** @var list<string> */
    private array $templateNames;

    /** @var list<string> */
    private array $pageTemplateNames;

    private string $layoutTemplateName;

    /** @var array<string, Template> */
    private array $interpretedTemplates;

    /** @var array<string, CompiledTemplateInterface> */
    private array $compiledTemplates;

    /** @var array<string, string> */
    private array $artifactPaths;

    private string $freshLoadScript;

    /**
     * @var list<array<string, array{page: array<string, mixed>, layout: array<string, mixed>}>>
     */
    private array $renderDataSets;

    /**
     * @var list<array<string, array{page: array<string, mixed>, layout: array<string, mixed>}>>
     */
    private array $correctnessDataSets;

    private int $dataSetIndex = 0;

    public function setUp(): void
    {
        $this->templateNames = StorefrontTheme::templateNames();
        $this->pageTemplateNames = StorefrontTheme::pageTemplateNames();
        $this->layoutTemplateName = StorefrontTheme::layoutTemplateName();
        $this->artifactDirectory = sys_get_temp_dir().'/php-liquid-compiler-'.bin2hex(random_bytes(8));

        if (! mkdir($this->artifactDirectory, 0755, true) && ! is_dir($this->artifactDirectory)) {
            throw new \RuntimeException('Could not create the compiler benchmark artifact directory.');
        }

        $this->interpretedEnvironment = StorefrontTheme::environmentFactory()
            ->setTemplatesCache(new MemoryTemplatesCache)
            ->build();
        $this->compiledEnvironment = StorefrontTheme::environmentFactory()
            ->setTemplatesCache(new MemoryTemplatesCache)
            ->build();
        $this->interpretedTemplates = [];
        $this->compiledTemplates = [];
        $this->artifactPaths = [];
        $this->dataSetIndex = 0;

        // Read and parse fixture sources before the benchmark subjects run.
        foreach ($this->templateNames as $templateName) {
            $source = StorefrontTheme::templateSource($templateName);
            $template = $this->interpretedEnvironment->parseString($source, $templateName);
            $this->interpretedTemplates[$templateName] = $template;
            $this->interpretedEnvironment->templatesCache->set($templateName, $template);

            $artifactPath = $this->artifactDirectory.'/'.str_replace('.', '_', $templateName).'.php';
            $this->artifactPaths[$templateName] = $artifactPath;
            $this->compiledEnvironment->compile($template, $artifactPath);
            $compiledTemplate = $this->loadCompiledArtifact($artifactPath);
            $this->compiledTemplates[$templateName] = $compiledTemplate;
            $this->compiledEnvironment->templatesCache->set($templateName, $compiledTemplate);
        }

        $this->writeFreshLoadScript();

        // Keep fixture/data creation out of render and stream timing.
        $this->renderDataSets = $this->buildRenderDataSets(self::DATA_SET_COUNT);
        $this->correctnessDataSets = $this->buildRenderDataSets(4);

        $this->assertCorrectness();
        $this->dataSetIndex = 0;
    }

    public function tearDown(): void
    {
        foreach ($this->artifactPaths as $artifactPath) {
            if (is_file($artifactPath)) {
                unlink($artifactPath);
            }
        }

        if (is_file($this->freshLoadScript)) {
            unlink($this->freshLoadScript);
        }

        if (is_dir($this->artifactDirectory)) {
            rmdir($this->artifactDirectory);
        }
    }

    public function benchCompileWrite(): void
    {
        foreach ($this->interpretedTemplates as $templateName => $template) {
            $this->compiledEnvironment->compile($template, $this->artifactPaths[$templateName]);
        }
    }

    #[BeforeMethods('prepareFreshArtifactLoad')]
    public function benchFreshArtifactLoad(): void
    {
        $output = [];
        $exitCode = 0;
        exec(
            escapeshellarg(PHP_BINARY).' '.escapeshellarg($this->freshLoadScript),
            $output,
            $exitCode,
        );

        if ($exitCode !== 0) {
            throw new \RuntimeException('Fresh compiled artifact load failed.');
        }
    }

    /**
     * Prepare filesystem metadata before PHPBench starts timing the isolated load.
     */
    public function prepareFreshArtifactLoad(): void
    {
        foreach ($this->artifactPaths as $artifactPath) {
            clearstatcache(true, $artifactPath);

            if (function_exists('opcache_invalidate')) {
                opcache_invalidate($artifactPath, true);
            }
        }
    }

    public function benchCompiledRender(): void
    {
        $renderData = $this->nextRenderDataSet();

        foreach ($this->pageTemplateNames as $pageTemplateName) {
            $this->renderPage(
                $this->compiledEnvironment,
                $this->compiledTemplates,
                $pageTemplateName,
                $renderData[$pageTemplateName],
            );
        }
    }

    public function benchCompiledStream(): void
    {
        $renderData = $this->nextRenderDataSet();

        foreach ($this->pageTemplateNames as $pageTemplateName) {
            $this->drain($this->streamPage(
                $this->compiledEnvironment,
                $this->compiledTemplates,
                $pageTemplateName,
                $renderData[$pageTemplateName],
            ));
        }
    }

    public function benchInterpretedRender(): void
    {
        $renderData = $this->nextRenderDataSet();

        foreach ($this->pageTemplateNames as $pageTemplateName) {
            $this->renderPage(
                $this->interpretedEnvironment,
                $this->interpretedTemplates,
                $pageTemplateName,
                $renderData[$pageTemplateName],
            );
        }
    }

    public function benchInterpretedStream(): void
    {
        $renderData = $this->nextRenderDataSet();

        foreach ($this->pageTemplateNames as $pageTemplateName) {
            $this->drain($this->streamPage(
                $this->interpretedEnvironment,
                $this->interpretedTemplates,
                $pageTemplateName,
                $renderData[$pageTemplateName],
            ));
        }
    }

    /**
     * @param  array<string, Template>  $templates
     * @param  array{page: array<string, mixed>, layout: array<string, mixed>}  $renderData
     */
    private function renderPage(
        Environment $environment,
        array $templates,
        string $pageTemplateName,
        array $renderData,
    ): string {
        $content = $templates[$pageTemplateName]->render(
            $environment->newRenderContext(staticData: $renderData['page']),
        );

        return $templates[$this->layoutTemplateName]->render($environment->newRenderContext(
            staticData: [
                ...$renderData['layout'],
                'content_for_layout' => $content,
            ],
        ));
    }

    /**
     * @param  array<string, Template>  $templates
     * @param  array{page: array<string, mixed>, layout: array<string, mixed>}  $renderData
     * @return \Generator<string>
     */
    private function streamPage(
        Environment $environment,
        array $templates,
        string $pageTemplateName,
        array $renderData,
    ): \Generator {
        $content = $templates[$pageTemplateName]->stream(
            $environment->newRenderContext(staticData: $renderData['page']),
        );

        return $templates[$this->layoutTemplateName]->stream($environment->newRenderContext(
            staticData: [
                ...$renderData['layout'],
                'content_for_layout' => $content,
            ],
        ));
    }

    /**
     * @return array<string, array{page: array<string, mixed>, layout: array<string, mixed>}>
     */
    private function nextRenderDataSet(): array
    {
        $renderData = $this->renderDataSets[$this->dataSetIndex % self::DATA_SET_COUNT];
        $this->dataSetIndex++;

        return $renderData;
    }

    /**
     * @return list<array<string, array{page: array<string, mixed>, layout: array<string, mixed>}>>
     */
    private function buildRenderDataSets(int $count): array
    {
        $renderDataSets = [];
        for ($dataSet = 0; $dataSet < $count; $dataSet++) {
            $renderData = [];
            foreach ($this->pageTemplateNames as $pageTemplateName) {
                $renderData[$pageTemplateName] = StorefrontTheme::renderData($pageTemplateName);
            }
            $renderDataSets[] = $renderData;
        }

        return $renderDataSets;
    }

    private function loadCompiledArtifact(string $artifactPath): CompiledTemplateInterface
    {
        $template = require $artifactPath;

        if (! $template instanceof CompiledTemplateInterface) {
            throw new \RuntimeException("Invalid compiler benchmark artifact: {$artifactPath}");
        }

        return $template;
    }

    private function writeFreshLoadScript(): void
    {
        $this->freshLoadScript = $this->artifactDirectory.'/fresh-load.php';
        $source = "<?php\n"
            .'require '.var_export(dirname(__DIR__, 2).'/vendor/autoload.php', true).";\n"
            .'$paths = '.var_export(array_values($this->artifactPaths), true).";\n"
            ."foreach (\$paths as \$path) {\n"
            ."    \$template = require \$path;\n"
            ."    if (! \$template instanceof \\Keepsuit\\Liquid\\Compiler\\CompiledTemplateInterface) {\n"
            ."        exit(1);\n"
            ."    }\n"
            ."}\n";

        if (file_put_contents($this->freshLoadScript, $source) !== strlen($source)) {
            throw new \RuntimeException('Unable to create fresh artifact load script.');
        }
    }

    /**
     * @param  \Generator<string>  $stream
     */
    private function drain(\Generator $stream): void
    {
        while ($stream->valid()) {
            $stream->next();
        }
    }

    private function assertCorrectness(): void
    {
        foreach (array_slice($this->correctnessDataSets, 0, 2) as $renderData) {
            foreach ($this->pageTemplateNames as $pageTemplateName) {
                $expected = $this->renderPage(
                    $this->interpretedEnvironment,
                    $this->interpretedTemplates,
                    $pageTemplateName,
                    $renderData[$pageTemplateName],
                );
                $actual = $this->renderPage(
                    $this->compiledEnvironment,
                    $this->compiledTemplates,
                    $pageTemplateName,
                    $renderData[$pageTemplateName],
                );

                if ($actual !== $expected) {
                    throw new \RuntimeException("Compiled render mismatch for {$pageTemplateName}.");
                }
            }
        }

        foreach (array_slice($this->correctnessDataSets, 2, 2) as $renderData) {
            foreach ($this->pageTemplateNames as $pageTemplateName) {
                $expected = $this->collect($this->streamPage(
                    $this->interpretedEnvironment,
                    $this->interpretedTemplates,
                    $pageTemplateName,
                    $renderData[$pageTemplateName],
                ));
                $actual = $this->collect($this->streamPage(
                    $this->compiledEnvironment,
                    $this->compiledTemplates,
                    $pageTemplateName,
                    $renderData[$pageTemplateName],
                ));

                if ($actual !== $expected) {
                    throw new \RuntimeException("Compiled stream mismatch for {$pageTemplateName}.");
                }
            }
        }
    }

    /**
     * @param  \Generator<string>  $stream
     */
    private function collect(\Generator $stream): string
    {
        $output = '';
        foreach ($stream as $chunk) {
            $output .= $chunk;
        }

        return $output;
    }
}
