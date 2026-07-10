<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Exceptions\SyntaxException;
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
        try {
            $this->variableName = $context->params->simpleVariableName();

            $context->params->assertEnd();
        } catch (SyntaxException $e) {
            throw SyntaxException::tagSyntaxException(static::tagName(), sprintf('%s <var>', static::tagName()), $e);
        }

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
