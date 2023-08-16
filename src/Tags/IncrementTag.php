<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Context;
use Keepsuit\Liquid\Tag;
use Keepsuit\Liquid\Tokenizer;

class IncrementTag extends Tag
{
    protected string $variableName;

    public static function tagName(): string
    {
        return 'increment';
    }

    public function parse(Tokenizer $tokenizer): static
    {
        parent::parse($tokenizer);

        $this->variableName = trim($this->markup);

        return $this;
    }

    public function render(Context $context): string
    {
        $counter = $context->getEnvironment($this->variableName) ?? -1;
        $counter += 1;
        $context->setEnvironment($this->variableName, $counter);

        return (string) $counter;
    }
}
