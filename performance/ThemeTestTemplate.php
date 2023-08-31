<?php

namespace Keepsuit\Liquid\Performance;

use Keepsuit\Liquid\TemplateFactory;

class ThemeTestTemplate
{
    public function __construct(
        protected TemplateFactory $factory,
        public string $templateName,
        public string $liquid,
        public ?string $layoutLiquid,
    ) {
    }

    public function pageTemplate(): string
    {
        return basename($this->templateName);
    }

    public function compile(): CompiledThemeTestTemplate
    {
        $template = $this->factory->parse($this->liquid);
        $layout = $this->layoutLiquid !== null ? $this->factory->parse($this->layoutLiquid) : null;

        return new CompiledThemeTestTemplate(
            templateName: $this->templateName,
            template: $template,
            layout: $layout,
        );
    }
}
