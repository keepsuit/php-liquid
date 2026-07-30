<?php

namespace Keepsuit\Liquid\Performance\benchmarks;

use Keepsuit\Liquid\Environment;
use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\Performance\Support\Database;
use Keepsuit\Liquid\Performance\Support\Drops\ProductDrop;
use Keepsuit\Liquid\Template;
use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\Groups;
use PhpBench\Attributes\Iterations;
use PhpBench\Attributes\OutputMode;
use PhpBench\Attributes\OutputTimeUnit;
use PhpBench\Attributes\Revs;

#[Groups(['micro'])]
#[Iterations(10)]
#[Revs(20)]
#[OutputMode('throughput')]
#[OutputTimeUnit('seconds', precision: 3)]
#[BeforeMethods('setUp')]
class OperationBench
{
    private Environment $environment;

    private Template $scalarTemplate;

    private Template $nestedTemplate;

    private Template $filterWithoutArgumentsTemplate;

    private Template $filterWithArgumentsTemplate;

    private ProductDrop $productDrop;

    public function setUp(): void
    {
        $this->environment = EnvironmentFactory::new()->build();
        $this->scalarTemplate = $this->environment->parseString(str_repeat('{{ value }}', 64));
        $this->nestedTemplate = $this->environment->parseString(str_repeat('{{ product.title }}', 64));
        $this->filterWithoutArgumentsTemplate = $this->environment->parseString(str_repeat('{{ value | upcase | escape }}', 32));
        $this->filterWithArgumentsTemplate = $this->environment->parseString(str_repeat('{{ value | append: suffix | replace: from, to }}', 32));
        $this->productDrop = Database::product();
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
            staticData: ['product' => $this->productDrop],
        ));
    }

    public function benchDropStream(): void
    {
        $this->drain($this->nestedTemplate->stream($this->environment->newRenderContext(
            staticData: ['product' => $this->productDrop],
        )));
    }

    public function benchFilterWithoutArguments(): void
    {
        $this->filterWithoutArgumentsTemplate->render($this->filterContext());
    }

    public function benchFilterWithArguments(): void
    {
        $this->filterWithArgumentsTemplate->render($this->filterContext());
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

    private function filterContext(): \Keepsuit\Liquid\Render\RenderContext
    {
        return $this->environment->newRenderContext(staticData: [
            'value' => 'example',
            'suffix' => '-suffix',
            'from' => 'example',
            'to' => 'value',
        ]);
    }
}
