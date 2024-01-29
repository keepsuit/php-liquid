<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Condition\Condition;
use Keepsuit\Liquid\Nodes\TagParseContext;
use Keepsuit\Liquid\Render\RenderContext;

class UnlessTag extends IfTag
{
    protected ?Condition $unlessCondition;

    public static function tagName(): string
    {
        return 'unless';
    }

    public function parse(TagParseContext $context): static
    {
        parent::parse($context);

        if ($context->tag === static::tagName()) {
            $this->unlessCondition = array_shift($this->conditions);
        }

        return $this;
    }

    public function render(RenderContext $context): string
    {
        $result = $this->unlessCondition?->evaluate($context);

        if (! $result) {
            return $this->unlessCondition?->body?->render($context) ?? '';
        }

        return parent::render($context);
    }

    public function parseTreeVisitorChildren(): array
    {
        return [$this->unlessCondition, ...$this->conditions];
    }
}
