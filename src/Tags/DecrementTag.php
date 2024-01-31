<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Render\RenderContext;

class DecrementTag extends IncrementTag
{
    protected string $variableName;

    public static function tagName(): string
    {
        return 'decrement';
    }

    public function render(RenderContext $context): string
    {
        $counter = $context->getEnvironment($this->variableName) ?? 0;
        $counter -= 1;
        $context->setEnvironment($this->variableName, $counter);

        return (string) $counter;
    }
}
