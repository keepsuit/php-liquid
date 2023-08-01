<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\ParseContext;
use Keepsuit\Liquid\Regex;
use Keepsuit\Liquid\SyntaxException;
use Keepsuit\Liquid\Tag;

class RenderTag extends Tag implements HasParseTreeVisitorChildren
{
    protected const Syntax = '/('.Regex::QuotedString.'+)(\s+(with|for)\s+('.Regex::QuotedFragment.'+))?(\s+(?:as)\s+('.Regex::VariableSegment.'+))?/';

    protected mixed $templateNameExpression;

    protected mixed $variableNameExpression;

    protected ?string $aliasName;

    protected array $attributes = [];

    public readonly bool $isForLoop;

    public function __construct(string $markup, ParseContext $parseContext)
    {
        parent::__construct($markup, $parseContext);

        if (! preg_match(static::Syntax, $markup, $matches)) {
            throw new SyntaxException($parseContext->locale->translate('errors.syntax.render'));
        }

        $this->templateNameExpression = $this->parseExpression($matches[1]);
        $this->aliasName = $matches[6] ?? null;
        $this->variableNameExpression = ($matches[4] ?? null) ? $this->parseExpression($matches[4]) : null;
        $this->isForLoop = $matches[3] === 'for';

        preg_match_all(sprintf('/%s/', Regex::TagAttributes), $markup, $attributeMatches, PREG_SET_ORDER);
        foreach ($attributeMatches as $matches) {
            $this->attributes[$matches[1]] = $this->parseExpression($matches[2]);
        }
    }

    public static function tagName(): string
    {
        return 'render';
    }

    public function parseTreeVisitorChildren(): array
    {
        return [
            $this->templateNameExpression,
            $this->variableNameExpression,
            ...$this->attributes,
        ];
    }
}
