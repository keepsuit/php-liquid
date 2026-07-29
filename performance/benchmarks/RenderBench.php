<?php

namespace Keepsuit\Liquid\Performance\benchmarks;

use Keepsuit\Liquid\Environment;
use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\Performance\Shopify\DatabaseDrop;
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
class RenderBench
{
    private Environment $environment;

    private Template $scalarTemplate;

    private Template $nestedTemplate;

    public function setUp(): void
    {
        $this->environment = EnvironmentFactory::new()->build();
        $this->scalarTemplate = $this->environment->parseString(str_repeat('{{ value }}', 64));
        $this->nestedTemplate = $this->environment->parseString(str_repeat('{{ product.title }}', 64));
    }

    public function benchScalarRender(): void
    {
        $this->scalarTemplate->render($this->environment->newRenderContext(
            staticData: ['value' => 'value'],
        ));
    }

    public function benchScalarStream(): void
    {
        $this->drain($this->scalarTemplate->stream($this->environment->newRenderContext(
            staticData: ['value' => 'value'],
        )));
    }

    public function benchNestedArrayRender(): void
    {
        $this->nestedTemplate->render($this->environment->newRenderContext(
            staticData: ['product' => ['title' => 'Product title']],
        ));
    }

    public function benchNestedArrayStream(): void
    {
        $this->drain($this->nestedTemplate->stream($this->environment->newRenderContext(
            staticData: ['product' => ['title' => 'Product title']],
        )));
    }

    public function benchDropRender(): void
    {
        $this->nestedTemplate->render($this->environment->newRenderContext(
            staticData: ['product' => new DatabaseDrop(['title' => 'Product title'])],
        ));
    }

    public function benchDropStream(): void
    {
        $this->drain($this->nestedTemplate->stream($this->environment->newRenderContext(
            staticData: ['product' => new DatabaseDrop(['title' => 'Product title'])],
        )));
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
