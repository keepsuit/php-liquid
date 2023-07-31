<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\ParseContext;
use Keepsuit\Liquid\Regex;
use Keepsuit\Liquid\SyntaxException;
use Keepsuit\Liquid\Tag;
use Keepsuit\Liquid\Variable;

class AssignTag extends Tag implements HasParseTreeVisitorChildren
{
    protected const Syntax = '/('.Regex::VariableSignature.'+)\s*=\s*(.*)\s*/m';

    protected string $to;

    protected Variable $from;

    public function __construct(string $tagName, string $markup, ParseContext $parseContext)
    {
        parent::__construct($tagName, $markup, $parseContext);

        if (preg_match(static::Syntax, $markup, $matches)) {
            $this->to = $matches[1];
            $this->from = new Variable($matches[2], $parseContext);
        } else {
            throw new SyntaxException($parseContext->locale->translate('errors.syntax.assign'));
        }
    }

    public static function name(): string
    {
        return 'assign';
    }

    public function parseTreeVisitorChildren(): array
    {
        return [$this->from];
    }
}
