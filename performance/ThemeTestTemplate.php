<?php

namespace Keepsuit\Liquid\Performance;

use Keepsuit\Liquid\Template;

class ThemeTestTemplate
{
    public function __construct(
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
        $template = Template::parse($this->liquid);
        $layout = $this->layoutLiquid !== null ? Template::parse($this->layoutLiquid) : null;

        return new CompiledThemeTestTemplate(
            templateName: $this->templateName,
            template: $template,
            layout: $layout,
        );
    }
}
