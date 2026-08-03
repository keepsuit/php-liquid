<?php

namespace Keepsuit\Liquid\Performance\benchmarks;

use Keepsuit\Liquid\Environment;
use Keepsuit\Liquid\Performance\Support\StorefrontTheme;
use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\Groups;
use PhpBench\Attributes\Iterations;
use PhpBench\Attributes\OutputMode;
use PhpBench\Attributes\OutputTimeUnit;
use PhpBench\Attributes\Revs;

/**
 * Whole-pipeline canary for the storefront theme.
 *
 * This class answers "did rendering get slower", not "what got slower": it
 * averages 29 templates across four pages, so it cannot localize a regression.
 * Per-feature sensitivity belongs in the operations group.
 *
 * ponytail: the operations group has not caught up yet, so nothing in the suite
 * isolates a single tag, the drop miss path, or superlinear growth. The deferred
 * list is in performance/README.md.
 */
#[Groups(['default'])]
#[Iterations(10)]
#[Revs(20)]
#[OutputMode('throughput')]
#[OutputTimeUnit('seconds', precision: 3)]
#[BeforeMethods('setUp')]
class ThemeBench
{
    private Environment $environment;

    /**
     * Sources are read up front: reading them inside a benchmark would measure
     * the filesystem instead of the tokenizer and the parser.
     *
     * @var array<string, string>
     */
    private array $sources;

    /** @var list<string> */
    private array $pageTemplateNames;

    public function setUp(): void
    {
        $this->environment = StorefrontTheme::environment();
        $this->sources = [];

        foreach (StorefrontTheme::templateNames() as $name) {
            $this->environment->parseTemplate($name);
            $this->sources[$name] = StorefrontTheme::templateSource($name);
        }

        $this->pageTemplateNames = StorefrontTheme::pageTemplateNames();
    }

    public function benchTokenize(): void
    {
        foreach ($this->sources as $source) {
            $this->environment->newParseContext()->tokenize($source);
        }
    }

    public function benchParse(): void
    {
        foreach ($this->sources as $name => $source) {
            $this->environment->parseString($source, $name);
        }
    }

    public function benchRender(): void
    {
        foreach ($this->pageTemplateNames as $pageTemplateName) {
            StorefrontTheme::renderPage($this->environment, $pageTemplateName);
        }
    }

    public function benchStream(): void
    {
        foreach ($this->pageTemplateNames as $pageTemplateName) {
            $this->drain(StorefrontTheme::streamPage($this->environment, $pageTemplateName));
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
}
