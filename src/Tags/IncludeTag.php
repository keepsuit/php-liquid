<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Regex;
use Keepsuit\Liquid\Tag;
use Keepsuit\Liquid\Tokenizer;

class IncludeTag extends Tag implements HasParseTreeVisitorChildren
{
    protected const Syntax = '/('.Regex::QuotedFragment.'+)(\s+(?:with|for)\s+('.Regex::QuotedFragment.'+))?(\s+(?:as)\s+('.Regex::VariableSegment.'+))?/';

    protected mixed $templateNameExpression;

    protected mixed $variableNameExpression;

    protected ?string $aliasName;

    protected array $attributes = [];

    public static function tagName(): string
    {
        return 'include';
    }

    public function parse(Tokenizer $tokenizer): static
    {
        parent::parse($tokenizer);

        if (preg_match(static::Syntax, $this->markup, $matches)) {
            $templateName = $matches[1];
            $variableName = $matches[3] ?? null;

            $this->aliasName = $matches[5] ?? null;
            $this->variableNameExpression = $variableName ? $this->parseExpression($variableName) : null;
            $this->templateNameExpression = $this->parseExpression($templateName);

            preg_match_all(sprintf('/%s/', Regex::TagAttributes), $this->markup, $attributeMatches, PREG_SET_ORDER);

            foreach ($attributeMatches as $matches) {
                $this->attributes[$matches[1]] = $this->parseExpression($matches[2]);
            }
        } else {
            throw new SyntaxException($this->parseContext->locale->translate('errors.syntax.include'));
        }

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
