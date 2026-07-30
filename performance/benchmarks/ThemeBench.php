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

    private Template $page;

    private Template $layout;

    public function setUp(): void
    {
        $this->environment = ComplexThemeFixture::environment();

        foreach (ComplexThemeFixture::templateNames() as $name) {
            $this->environment->parseTemplate($name);
        }

        $this->page = $this->environment->parseTemplate(ComplexThemeFixture::rootTemplateName());
        $this->layout = $this->environment->parseTemplate(ComplexThemeFixture::LAYOUT_TEMPLATE_NAME);
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
        $content = $this->page->render(ComplexThemeFixture::newRenderContext($this->environment));

        $this->layout->render(ComplexThemeFixture::newLayoutRenderContext($this->environment, $content));
    }

    public function benchStream(): void
    {
        $content = $this->page->stream(ComplexThemeFixture::newRenderContext($this->environment));

        foreach ($this->layout->stream(ComplexThemeFixture::newLayoutRenderContext($this->environment, $content)) as $chunk) {
        }
    }
}
