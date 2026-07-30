<?php

namespace Keepsuit\Liquid\Performance\benchmarks;

use Keepsuit\Liquid\Environment;
use Keepsuit\Liquid\Performance\benchmarks\Support\ComplexThemeFixture;
use Keepsuit\Liquid\Template;
use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\Groups;
use PhpBench\Attributes\Iterations;
use PhpBench\Attributes\OutputMode;
use PhpBench\Attributes\OutputTimeUnit;
use PhpBench\Attributes\Revs;

#[Groups(['macro'])]
#[Iterations(20)]
#[Revs(1000)]
#[OutputMode('throughput')]
#[OutputTimeUnit('seconds', precision: 3)]
#[BeforeMethods('setUp')]
class ThemeBench
{
    private Environment $environment;

    private Template $template;

    public function setUp(): void
    {
        $this->environment = ComplexThemeFixture::environment();

        foreach (array_keys(ComplexThemeFixture::templateSources()) as $name) {
            $this->environment->parseTemplate($name);
        }

        $this->template = $this->environment->parseTemplate(ComplexThemeFixture::rootTemplateName());
    }

    public function benchTokenize(): void
    {
        foreach (ComplexThemeFixture::templateSources() as $source) {
            $this->environment->newParseContext()->tokenize($source);
        }
    }

    public function benchParse(): void
    {
        foreach (ComplexThemeFixture::templateSources() as $name => $source) {
            $this->environment->parseString($source, $name);
        }
    }

    public function benchRender(): void
    {
        $this->template->render(ComplexThemeFixture::newRenderContext($this->environment));
    }

    #[Revs(1500)]
    public function benchStream(): void
    {
        foreach ($this->template->stream(ComplexThemeFixture::newRenderContext($this->environment)) as $chunk) {
        }
    }
}
