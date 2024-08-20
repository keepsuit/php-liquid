<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Nodes\VariableLookup;
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
        $variableName = $context->params->expression();
        $this->variableName = match (true) {
            $variableName instanceof VariableLookup || is_string($variableName) => (string) $variableName,
            default => throw new SyntaxException('Invalid variable name'),
        };

        $context->params->assertEnd();

        return $this;
    }

    public function render(RenderContext $context): string
    {
        $counter = $context->getEnvironment($this->variableName);

        $counter = is_int($counter) ? $counter + 1 : 0;

        $context->setEnvironment($this->variableName, $counter);

        return (string) $counter;
    }
}
