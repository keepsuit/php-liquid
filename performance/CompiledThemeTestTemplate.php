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
        if ($this->layout) {
            $this->layout->render($this->buildContext([
                ...$assigns,
                'content_for_layout' => $this->template->render($this->buildContext($assigns)),
            ]));
        } else {
            $this->template->render($this->buildContext($assigns));
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
