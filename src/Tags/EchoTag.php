<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Context;
use Keepsuit\Liquid\Contracts\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Tag;
use Keepsuit\Liquid\Tokenizer;
use Keepsuit\Liquid\Variable;

class EchoTag extends Tag implements HasParseTreeVisitorChildren
{
    protected Variable $variable;

    public function parse(Tokenizer $tokenizer): static
    {
        parent::parse($tokenizer);

        $this->variable = new Variable($this->markup, $this->parseContext);

        return $this;
    }

    public static function tagName(): string
    {
        return 'echo';
    }

    public function blank(): bool
    {
        return true;
    }

    public function parseTreeVisitorChildren(): array
    {
        return [$this->variable];
    }

    public function render(Context $context): string
    {
        return $this->variable->render($context);
    }
}
