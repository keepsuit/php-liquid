<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\ParseContext;
use Keepsuit\Liquid\Regex;
use Keepsuit\Liquid\SyntaxException;
use Keepsuit\Liquid\Tag;
use Keepsuit\Liquid\Tokenizer;

class IncludeTag extends Tag implements HasParseTreeVisitorChildren
{
    protected const Syntax = '/('.Regex::QuotedFragment.'+)(\s+(?:with|for)\s+('.Regex::QuotedFragment.'+))?(\s+(?:as)\s+('.Regex::VariableSegment.'+))?/';

    protected mixed $templateNameExpression;

    protected mixed $variableNameExpression;

    protected ?string $aliasName;

    protected array $attributes = [];

    public function __construct(string $tagName, string $markup, ParseContext $parseContext)
    {
        parent::__construct($tagName, $markup, $parseContext);

        if (preg_match(static::Syntax, $markup, $matches)) {
            $templateName = $matches[1];
            $variableName = $matches[3] ?? null;

            $this->aliasName = $matches[5] ?? null;
            $this->variableNameExpression = $variableName ? $this->parseExpression($variableName) : null;
            $this->templateNameExpression = $this->parseExpression($templateName);

            preg_match_all(sprintf('/%s/', Regex::TagAttributes), $markup, $attributeMatches, PREG_SET_ORDER);

            foreach ($attributeMatches as $matches) {
                $this->attributes[$matches[1]] = $this->parseExpression($matches[2]);
            }
        } else {
            throw new SyntaxException($parseContext->locale->translate('errors.syntax.include'));
        }
    }

    public static function name(): string
    {
        return 'include';
    }

    public function parse(Tokenizer $tokenizer): static
    {
        return $this;
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
