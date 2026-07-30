<?php

namespace Keepsuit\Liquid\Performance\benchmarks;

use Keepsuit\Liquid\Environment;
use Keepsuit\Liquid\Performance\benchmarks\Support\ComplexThemeFixture;
use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\Groups;
use PhpBench\Attributes\Iterations;
use PhpBench\Attributes\OutputMode;
use PhpBench\Attributes\OutputTimeUnit;
use PhpBench\Attributes\Revs;

#[Groups(['macro'])]
#[Iterations(10)]
#[Revs(20)]
#[OutputMode('throughput')]
#[OutputTimeUnit('seconds', precision: 3)]
#[BeforeMethods('setUp')]
class ThemeBench
{
    private Environment $environment;

    /**
     * Template sources are read up front: reading them inside the benchmark
     * would measure the filesystem instead of the tokenizer and the parser.
     *
     * @var array<string, string>
     */
    private array $sources;

    public function setUp(): void
    {
        $this->environment = ComplexThemeFixture::environment();
        $this->sources = [];

        foreach (ComplexThemeFixture::templateNames() as $name) {
            $this->environment->parseTemplate($name);
            $this->sources[$name] = ComplexThemeFixture::templateSource($name);
        }
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
        foreach (ComplexThemeFixture::pageTemplateNames() as $templateName) {
            ComplexThemeFixture::renderPage($this->environment, $templateName);
        }
    }

    public function benchStream(): void
    {
        foreach (ComplexThemeFixture::pageTemplateNames() as $templateName) {
            foreach (ComplexThemeFixture::streamPage($this->environment, $templateName) as $chunk) {
            }
        }
    }
}
