<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Contracts\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Nodes\Variable;
use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\Parse\Tokenizer;
use Keepsuit\Liquid\Render\Context;
use Keepsuit\Liquid\Tag;

class EchoTag extends Tag implements HasParseTreeVisitorChildren
{
    protected Variable $variable;

    public function parse(ParseContext $parseContext, Tokenizer $tokenizer): static
    {
        parent::parse($parseContext, $tokenizer);

        $this->variable = Variable::fromMarkup($this->markup, $parseContext->lineNumber);

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
