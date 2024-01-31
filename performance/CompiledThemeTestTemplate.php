<?php

namespace Keepsuit\Liquid\Performance;

use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Template;
use Keepsuit\Liquid\TemplateFactory;

class CompiledThemeTestTemplate
{
    public function __construct(
        protected TemplateFactory $factory,
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

    public function stream(array $assigns = []): void
    {
        $content = $this->template->stream($this->buildContext($assigns));

        if ($this->layout) {
            $content = $this->layout->stream($this->buildContext([
                ...$assigns,
                'content_for_layout' => $content,
            ]));
        }

        while ($content->valid()) {
            $content->next();
        }
    }

    protected function buildContext(array $assigns = []): RenderContext
    {
        return $this->factory->newRenderContext(
            staticEnvironment: $assigns,
        );
    }
}
