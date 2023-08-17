<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Condition;
use Keepsuit\Liquid\Context;
use Keepsuit\Liquid\Tokenizer;

class UnlessTag extends IfTag
{
    protected ?Condition $unlessCondition;

    public static function tagName(): string
    {
        return 'unless';
    }

    public function parse(Tokenizer $tokenizer): static
    {
        parent::parse($tokenizer);

        $this->unlessCondition = array_shift($this->conditions);

        return $this;
    }

    public function render(Context $context): string
    {
        $result = $this->unlessCondition?->evaluate($context);
        if (! $result) {
            return $this->unlessCondition?->attachment?->render($context) ?? '';
        }

        return parent::render($context);
    }

    public function parseTreeVisitorChildren(): array
    {
        return [$this->unlessCondition, ...$this->conditions];
    }
}
