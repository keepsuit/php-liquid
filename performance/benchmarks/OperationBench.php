<?php

namespace Keepsuit\Liquid\Performance\benchmarks;

use Keepsuit\Liquid\Environment;
use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\Performance\Support\Database;
use Keepsuit\Liquid\Performance\Support\Drops\ProductDrop;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Template;
use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\Groups;
use PhpBench\Attributes\Iterations;
use PhpBench\Attributes\OutputMode;
use PhpBench\Attributes\OutputTimeUnit;
use PhpBench\Attributes\Revs;

#[Groups(['operations'])]
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

    private Template $dropMethodTemplate;

    private Template $dropMethodMissingHitTemplate;

    private Template $dropMethodMissingMissTemplate;

    private Template $productListTemplate;

    private Template $conditionTemplate;

    private Template $assignTemplate;

    private Template $assignCompositeTemplate;

    private Template $captureTemplate;

    /** @var array<string, mixed> */
    private array $assignCompositeData;

    /**
     * Built in setUp, not in the subject: these two subjects measure how
     * Drop::__get resolves a property, and constructing a ProductDrop (variants,
     * images, metafields) inside the subject would swamp that with fixture cost.
     * Sharing one instance across revolutions is safe here only because the
     * template reads a public property and never a #[Cache]d method.
     */
    private ProductDrop $productDrop;

    /** @var ProductDrop[] */
    protected array $productList;

    public function setUp(): void
    {
        $this->environment = EnvironmentFactory::new()->build();
        $this->scalarTemplate = $this->environment->parseString(str_repeat('{{ value }}', 64));
        $this->nestedTemplate = $this->environment->parseString(str_repeat('{{ product.title }}', 64));
        $this->filterWithoutArgumentsTemplate = $this->environment->parseString(str_repeat('{{ value | upcase | escape }}', 32));
        $this->filterWithArgumentsTemplate = $this->environment->parseString(str_repeat('{{ value | append: suffix | replace: from, to }}', 32));
        $this->dropMethodTemplate = $this->environment->parseString(str_repeat('{{ product.url }}', 64));
        $this->dropMethodMissingHitTemplate = $this->environment->parseString(str_repeat('{{ product.metafields.material }}', 64));
        $this->dropMethodMissingMissTemplate = $this->environment->parseString(str_repeat('{{ product.metafields.unknown }}', 64));
        $this->productListTemplate = $this->environment->parseString('{% for product in products %}{{ product.title }}{% endfor %}');
        $this->conditionTemplate = $this->environment->parseString(str_repeat('{% if value == expected %}x{% endif %}', 64));
        $this->assignTemplate = $this->environment->parseString(str_repeat('{% assign value = value %}', 64));
        $this->assignCompositeTemplate = $this->environment->parseString(str_repeat('{% assign value = values %}', 16));
        $this->captureTemplate = $this->environment->parseString(str_repeat('{% capture value %}captured{% endcapture %}', 64));
        $this->assignCompositeData = [
            'title' => 'Product title',
            'tags' => ['one', 'two', 'three'],
            'metadata' => ['material' => 'cotton', 'color' => 'blue'],
        ];
        $this->productDrop = Database::product();
        $this->productList = Database::products();
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

    public function benchDropMethodRender(): void
    {
        $this->dropMethodTemplate->render($this->productContext());
    }

    public function benchDropMethodStream(): void
    {
        $this->drain($this->dropMethodTemplate->stream($this->productContext()));
    }

    public function benchDropMethodMissingHitRender(): void
    {
        $this->dropMethodMissingHitTemplate->render($this->productContext());
    }

    public function benchDropMethodMissingHitStream(): void
    {
        $this->drain($this->dropMethodMissingHitTemplate->stream($this->productContext()));
    }

    public function benchDropMethodMissingMissRender(): void
    {
        $this->dropMethodMissingMissTemplate->render($this->productContext());
    }

    public function benchDropMethodMissingMissStream(): void
    {
        $this->drain($this->dropMethodMissingMissTemplate->stream($this->productContext()));
    }

    public function benchProductListRender(): void
    {
        $this->productListTemplate->render($this->environment->newRenderContext(
            staticData: ['products' => $this->productList],
        ));
    }

    public function benchProductListStream(): void
    {
        $this->drain($this->productListTemplate->stream($this->environment->newRenderContext(
            staticData: ['products' => $this->productList],
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

    public function benchConditionRender(): void
    {
        $this->conditionTemplate->render($this->environment->newRenderContext(staticData: [
            'value' => 'value',
            'expected' => 'value',
        ]));
    }

    public function benchAssignRender(): void
    {
        $this->assignTemplate->render($this->environment->newRenderContext(staticData: [
            'value' => 'value',
        ]));
    }

    public function benchAssignCompositeRender(): void
    {
        $this->assignCompositeTemplate->render($this->environment->newRenderContext(staticData: [
            'values' => $this->assignCompositeData,
        ]));
    }

    public function benchCaptureRender(): void
    {
        $this->captureTemplate->render($this->environment->newRenderContext());
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

    private function filterContext(): RenderContext
    {
        return $this->environment->newRenderContext(staticData: [
            'value' => 'example',
            'suffix' => '-suffix',
            'from' => 'example',
            'to' => 'value',
        ]);
    }

    private function productContext(): RenderContext
    {
        return $this->environment->newRenderContext(staticData: ['product' => $this->productDrop]);
    }
}
