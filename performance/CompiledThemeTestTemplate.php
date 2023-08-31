<?php

namespace Keepsuit\Liquid\Performance;

use Keepsuit\Liquid\Performance\Shopify\CustomFilters;
use Keepsuit\Liquid\Render\Context;
use Keepsuit\Liquid\Template;

class CompiledThemeTestTemplate
{
    public function __construct(
        public string $templateName,
        public Template $template,
        public ?Template $layout,
    ) {
    }

    public function pageTemplate(): string
    {
        return basename($this->templateName);
    }

    public function render(array $assigns = []): void
    {
        $content = $this->template->render($this->buildContext($assigns));

        if ($this->layout) {
            $this->layout->render($this->buildContext([
                ...$assigns,
                'content_for_layout' => $content,
            ]));
        }
    }

    protected function buildContext(array $assigns = []): Context
    {
        return new Context(
            staticEnvironment: $assigns,
            filters: [
                CustomFilters::class,
            ],
        );
    }
}
