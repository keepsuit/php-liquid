<?php

namespace Keepsuit\Liquid\Performance;

use Keepsuit\Liquid\Environment;

class ThemeTestTemplate
{
    public function __construct(
        protected Environment $environment,
        public string $templateName,
        public string $liquid,
        public ?string $layoutLiquid,
    ) {}

    public function pageTemplate(): string
    {
        return basename($this->templateName);
    }

    public function compile(): CompiledThemeTestTemplate
    {
        $template = $this->environment->parseString($this->liquid);
        $layout = $this->layoutLiquid !== null ? $this->environment->parseString($this->layoutLiquid) : null;

        return new CompiledThemeTestTemplate(
            environment: $this->environment,
            templateName: $this->templateName,
            template: $template,
            layout: $layout,
        );
    }
}
