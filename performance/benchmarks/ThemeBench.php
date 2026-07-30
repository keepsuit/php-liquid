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
#[Iterations(10)]
#[Revs(20)]
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

        foreach (ComplexThemeFixture::templateNames() as $name) {
            $this->environment->parseTemplate($name);
        }

        $this->template = $this->environment->parseTemplate(ComplexThemeFixture::rootTemplateName());
    }

    public function benchTokenize(): void
    {
        foreach (ComplexThemeFixture::templateNames() as $name) {
            $this->environment->newParseContext()->tokenize(ComplexThemeFixture::templateSource($name));
        }
    }

    public function benchParse(): void
    {
        foreach (ComplexThemeFixture::templateNames() as $name) {
            $this->environment->parseString(ComplexThemeFixture::templateSource($name), $name);
        }
    }

    public function benchRender(): void
    {
        $this->template->render(ComplexThemeFixture::newRenderContext($this->environment));
    }

    public function benchStream(): void
    {
        foreach ($this->template->stream(ComplexThemeFixture::newRenderContext($this->environment)) as $chunk) {
        }
    }
}
