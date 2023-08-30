<?php

namespace Keepsuit\Liquid\Performance;

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

    public function render(): void
    {
        if ($this->layout) {
            $context = new Context(
                staticEnvironment: [
                    'content_for_layout' => $this->template->render(),
                ]
            );
            $this->layout->render($context);
        } else {
            $this->template->render();
        }
    }
}
