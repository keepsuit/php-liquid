<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Regex;
use Keepsuit\Liquid\SyntaxException;
use Keepsuit\Liquid\TagBlock;
use Keepsuit\Liquid\Tokenizer;

class TableRowTag extends TagBlock implements HasParseTreeVisitorChildren
{
    const Syntax = '/(\w+)\s+in\s+('.Regex::QuotedFragment.'+)/';

    protected string $variableName;

    protected mixed $collectionName;

    protected array $attributes = [];

    public function parse(Tokenizer $tokenizer): static
    {
        parent::parse($tokenizer);

        if (! preg_match(self::Syntax, $this->markup, $matches)) {
            throw new SyntaxException($this->parseContext->locale->translate('errors.syntax.table_row'));
        }

        $this->variableName = $matches[1];
        $this->collectionName = $this->parseExpression($matches[2]);

        preg_match_all(sprintf('/%s/', Regex::TagAttributes), $this->markup, $attributeMatches, PREG_SET_ORDER);

        foreach ($attributeMatches as $matches) {
            $this->attributes[$matches[1]] = $this->parseExpression($matches[2]);
        }

        return $this;
    }

    public static function tagName(): string
    {
        return 'tablerow';
    }

    public function parseTreeVisitorChildren(): array
    {
        return [
            ...$this->nodeList(),
            ...$this->attributes,
            $this->collectionName,
        ];
    }
}
