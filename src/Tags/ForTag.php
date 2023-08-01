<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Arr;
use Keepsuit\Liquid\Block;
use Keepsuit\Liquid\BlockBody;
use Keepsuit\Liquid\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\ParseContext;
use Keepsuit\Liquid\Parser;
use Keepsuit\Liquid\ParserSwitching;
use Keepsuit\Liquid\Regex;
use Keepsuit\Liquid\SyntaxException;
use Keepsuit\Liquid\Tokenizer;
use Keepsuit\Liquid\TokenType;

class ForTag extends Block implements HasParseTreeVisitorChildren
{
    use ParserSwitching;

    const Syntax = '/\A('.Regex::VariableSegment.'+)\s+in\s+('.Regex::QuotedFragment.'+)\s*(reversed)?/';

    protected string $variableName;

    protected mixed $collectionName;

    protected mixed $from = null;

    protected mixed $limit = null;

    protected BlockBody $forBlock;

    protected ?BlockBody $elseBlock = null;

    public function __construct(string $markup, ParseContext $parseContext)
    {
        parent::__construct($markup, $parseContext);

        $this->strictParseWithErrorModeFallback($markup, $parseContext);
    }

    public static function tagName(): string
    {
        return 'for';
    }

    public function parse(Tokenizer $tokenizer): static
    {
        $this->forBlock = $this->parseBody($tokenizer);

        //        if (! $this->forBlock->blank) {
        //            $this->elseBlock = $this->parseBody($tokenizer);
        //        }

        if ($this->blank()) {
            $this->elseBlock?->removeBlankStrings();
            $this->forBlock->removeBlankStrings();
        }

        return $this;
    }

    protected function strictParse(string $markup): mixed
    {
        $parser = new Parser($markup);

        $this->variableName = $parser->consume(TokenType::Identifier);

        if (! $parser->idOrFalse('in')) {
            throw new SyntaxException($this->parseContext->locale->translate('errors.syntax.for_invalid_in'));
        }

        $collectionNameMarkup = $parser->expression();
        $this->collectionName = $this->parseExpression($collectionNameMarkup);

        $name = sprintf('%s-%s', $this->variableName, $collectionNameMarkup);
        $reversed = $parser->idOrFalse('reversed') !== false;

        while ($parser->look(TokenType::Comma) || $parser->look(TokenType::Identifier)) {
            $parser->consumeOrFalse(TokenType::Comma);

            $attribute = $parser->idOrFalse('limit') ?: $parser->idOrFalse('offset');

            if (! $attribute) {
                throw new SyntaxException($this->parseContext->locale->translate('errors.syntax.for_invalid_attribute'));
            }

            $parser->consume(TokenType::Colon);

            $this->setAttribute($attribute, $parser->expression());
        }

        $parser->consume(TokenType::EndOfString);

        return $this;
    }

    protected function laxParse(string $markup): mixed
    {
        dd('laxParse', $markup);
    }

    protected function setAttribute(string $attribute, string $expression): void
    {
        if ($attribute === 'offset') {
            $this->from = $expression === 'continue' ? 'continue' : $this->parseExpression($expression);

            return;
        }

        if ($attribute === 'limit') {
            $this->limit = $this->parseExpression($expression);

            return;
        }
    }

    public function nodeList(): array
    {
        return $this->elseBlock ? [$this->forBlock, $this->elseBlock] : [$this->forBlock];
    }

    public function parseTreeVisitorChildren(): array
    {
        return Arr::compact([
            ...$this->nodeList(),
            $this->limit,
            $this->from,
            $this->collectionName,
        ]);
    }
}
