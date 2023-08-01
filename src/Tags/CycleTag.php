<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Arr;
use Keepsuit\Liquid\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\ParseContext;
use Keepsuit\Liquid\Regex;
use Keepsuit\Liquid\SyntaxException;
use Keepsuit\Liquid\Tag;

class CycleTag extends Tag implements HasParseTreeVisitorChildren
{
    protected const SimpleSyntax = '/\A'.Regex::QuotedFragment.'+/';

    protected const NamedSyntax = '/\A('.Regex::QuotedFragment.')\s*\:\s*(.*)/m';

    protected array $variables = [];

    public function __construct(string $markup, ParseContext $parseContext)
    {
        parent::__construct($markup, $parseContext);

        if (preg_match(static::NamedSyntax, $markup, $matches)) {
            //TODO: Implement named cycle syntax.
            throw new \RuntimeException('Named cycle syntax is not supported yet.');
        } elseif (preg_match(static::SimpleSyntax, $markup, $matches)) {
            $this->variables = $this->parseVariablesFromString($markup);
        } else {
            throw new SyntaxException($parseContext->locale->translate('errors.syntax.cycle'));
        }
    }

    public static function tagName(): string
    {
        return 'cycle';
    }

    protected function parseVariablesFromString(string $markup): array
    {
        $variables = explode(',', $markup);

        $variables = array_map(
            fn (string $var) => preg_match('/\s*('.Regex::QuotedFragment.')\s*/', $var, $matches)
                ? $this->parseExpression($matches[1])
                : null,
            $variables
        );

        return Arr::compact($variables);
    }

    public function parseTreeVisitorChildren(): array
    {
        return $this->variables;
    }
}
