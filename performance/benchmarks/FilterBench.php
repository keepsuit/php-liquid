<?php

namespace Keepsuit\Liquid\Performance\benchmarks;

use Keepsuit\Liquid\Environment;
use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\Template;
use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\Iterations;
use PhpBench\Attributes\OutputMode;
use PhpBench\Attributes\OutputTimeUnit;
use PhpBench\Attributes\Revs;

#[Iterations(10)]
#[Revs(20)]
#[OutputMode('throughput')]
#[OutputTimeUnit('seconds', precision: 3)]
#[BeforeMethods('setUp')]
class FilterBench
{
    private Environment $environment;

    private Template $noArgumentsTemplate;

    private Template $argumentsTemplate;

    public function setUp(): void
    {
        $this->environment = EnvironmentFactory::new()->build();
        $this->noArgumentsTemplate = $this->environment->parseString(str_repeat('{{ value | upcase | escape }}', 32));
        $this->argumentsTemplate = $this->environment->parseString(str_repeat('{{ value | append: suffix | replace: from, to }}', 32));
    }

    public function benchNoArguments(): void
    {
        $this->noArgumentsTemplate->render($this->context());
    }

    public function benchArguments(): void
    {
        $this->argumentsTemplate->render($this->context());
    }

    private function context(): \Keepsuit\Liquid\Render\RenderContext
    {
        return $this->environment->newRenderContext(staticData: [
            'value' => 'example',
            'suffix' => '-suffix',
            'from' => 'example',
            'to' => 'value',
        ]);
    }
}
