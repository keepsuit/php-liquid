<?php

namespace Keepsuit\Liquid\Performance\benchmarks;

use Keepsuit\Liquid\Performance\Shopify\CommentFormTag;
use Keepsuit\Liquid\Performance\Shopify\CustomFilters;
use Keepsuit\Liquid\Performance\Shopify\PaginateTag;
use Keepsuit\Liquid\Performance\ThemeRunner;
use Keepsuit\Liquid\TemplateFactory;
use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\Iterations;
use PhpBench\Attributes\OutputMode;
use PhpBench\Attributes\OutputTimeUnit;
use PhpBench\Attributes\Revs;

#[Iterations(10)]
#[Revs(10)]
#[OutputMode('throughput')]
#[OutputTimeUnit('seconds', precision: 3)]
#[BeforeMethods('setUp')]
class LiquidBench
{
    protected ThemeRunner $themeRunner;

    public function setUp(): void
    {
        $this->themeRunner = $this->getThemeRunner();
    }

    public function benchParsing(): void
    {
        $this->themeRunner->compile();
    }

    public function benchRender(): void
    {
        $this->themeRunner->render();
    }

    public function benchRenderAsync(): void
    {
        $this->themeRunner->renderAsync();
    }

    public function benchParsingAndRendering(): void
    {
        $this->themeRunner->run();
    }

    public function benchParsingAndRenderingAsync(): void
    {
        $this->themeRunner->runAsync();
    }

    protected function getThemeRunner(): ThemeRunner
    {
        $templateFactory = TemplateFactory::new()
            ->registerTag(CommentFormTag::class)
            ->registerTag(PaginateTag::class)
            ->registerFilter(CustomFilters::class);

        return new ThemeRunner($templateFactory);
    }
}
