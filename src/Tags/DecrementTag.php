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
        $counter = $context->getEnvironment($this->variableName);

        $counter = is_int($counter) ? $counter - 1 : -1;

        $context->setEnvironment($this->variableName, $counter);

        return (string) $counter;
    }
}
