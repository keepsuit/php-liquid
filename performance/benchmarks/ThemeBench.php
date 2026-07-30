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

    public function setUp(): void
    {
        $this->environment = ComplexThemeFixture::environment();

        foreach (ComplexThemeFixture::templateNames() as $name) {
            $this->environment->parseTemplate($name);
        }
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
