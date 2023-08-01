<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Block;
use Keepsuit\Liquid\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\ParseContext;
use Keepsuit\Liquid\Regex;
use Keepsuit\Liquid\SyntaxException;

class TableRowTag extends Block implements HasParseTreeVisitorChildren
{
    const Syntax = '/(\w+)\s+in\s+('.Regex::QuotedFragment.'+)/';

    protected string $variableName;

    protected mixed $collectionName;

    protected array $attributes = [];

    public function __construct(string $markup, ParseContext $parseContext)
    {
        parent::__construct($markup, $parseContext);

        if (! preg_match(self::Syntax, $markup, $matches)) {
            throw new SyntaxException($parseContext->locale->translate('errors.syntax.table_row'));
        }

        $this->variableName = $matches[1];
        $this->collectionName = $this->parseExpression($matches[2]);

        preg_match_all(sprintf('/%s/', Regex::TagAttributes), $markup, $attributeMatches, PREG_SET_ORDER);

        foreach ($attributeMatches as $matches) {
            $this->attributes[$matches[1]] = $this->parseExpression($matches[2]);
        }
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
