<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\ParseContext;
use Keepsuit\Liquid\Tag;
use Keepsuit\Liquid\Variable;

class EchoTag extends Tag implements HasParseTreeVisitorChildren
{
    public readonly Variable $variable;

    public function __construct(string $markup, ParseContext $parseContext)
    {
        parent::__construct($markup, $parseContext);

        $this->variable = new Variable($markup, $parseContext);
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
}
