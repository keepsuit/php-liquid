<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Parse\TagParseContext;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Tag;

class IncrementTag extends Tag
{
    protected string $variableName;

    public static function tagName(): string
    {
        return 'increment';
    }

    public function parse(TagParseContext $context): static
    {
        $this->variableName = $context->params->simpleVariableName('Invalid variable name');

        $context->params->assertEnd();

        return $this;
    }

    public function render(RenderContext $context): string
    {
        $counter = $context->getData($this->variableName);

        $counter = is_int($counter) ? $counter + 1 : 0;

        $context->setData($this->variableName, $counter);

        return (string) $counter;
    }
}
