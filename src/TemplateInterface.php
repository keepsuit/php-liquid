<?php

namespace Keepsuit\Liquid;

use Keepsuit\Liquid\Render\RenderContext;

interface TemplateInterface
{
    public function render(RenderContext $context): string;

    /**
     * @return \Generator<string>
     */
    public function stream(RenderContext $context): \Generator;

    public function getState(): TemplateSharedState;

    /**
     * @return array<\Throwable>
     */
    public function getErrors(): array;

    public function name(): ?string;
}
