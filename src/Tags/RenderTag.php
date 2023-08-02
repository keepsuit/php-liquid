<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Regex;
use Keepsuit\Liquid\SyntaxException;
use Keepsuit\Liquid\Tag;
use Keepsuit\Liquid\Tokenizer;

class RenderTag extends Tag implements HasParseTreeVisitorChildren
{
    protected const Syntax = '/('.Regex::QuotedString.'+)(\s+(with|for)\s+('.Regex::QuotedFragment.'+))?(\s+(?:as)\s+('.Regex::VariableSegment.'+))?/';

    protected mixed $templateNameExpression;

    protected mixed $variableNameExpression;

    protected ?string $aliasName;

    protected array $attributes = [];

    protected bool $isForLoop;

    public function parse(Tokenizer $tokenizer): static
    {
        parent::parse($tokenizer);

        if (! preg_match(static::Syntax, $this->markup, $matches)) {
            throw new SyntaxException($this->parseContext->locale->translate('errors.syntax.render'));
        }

        $this->templateNameExpression = $this->parseExpression($matches[1]);
        $this->aliasName = $matches[6] ?? null;
        $this->variableNameExpression = ($matches[4] ?? null) ? $this->parseExpression($matches[4]) : null;
        $this->isForLoop = $matches[3] === 'for';

        preg_match_all(sprintf('/%s/', Regex::TagAttributes), $this->markup, $attributeMatches, PREG_SET_ORDER);
        foreach ($attributeMatches as $matches) {
            $this->attributes[$matches[1]] = $this->parseExpression($matches[2]);
        }

        return $this;
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
