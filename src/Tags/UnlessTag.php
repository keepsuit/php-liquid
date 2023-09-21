<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Condition\Condition;
use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\Parse\Tokenizer;
use Keepsuit\Liquid\Render\Context;

class UnlessTag extends IfTag
{
    protected ?Condition $unlessCondition;

    public static function tagName(): string
    {
        return 'unless';
    }

    public function parse(ParseContext $parseContext, Tokenizer $tokenizer): static
    {
        parent::parse($parseContext, $tokenizer);

        $this->unlessCondition = array_shift($this->conditions);

        return $this;
    }

    public function renderAsync(Context $context): \Generator
    {
        $result = $this->unlessCondition?->evaluate($context);

        if (! $result) {
            yield from $this->unlessCondition?->attachment?->renderAsync($context) ?? [];

            return;
        }

        yield from parent::renderAsync($context);
    }

    public function parseTreeVisitorChildren(): array
    {
        return [$this->unlessCondition, ...$this->conditions];
    }
}
