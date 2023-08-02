<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Arr;
use Keepsuit\Liquid\BlockBodySection;
use Keepsuit\Liquid\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Parser;
use Keepsuit\Liquid\ParserSwitching;
use Keepsuit\Liquid\Regex;
use Keepsuit\Liquid\SyntaxException;
use Keepsuit\Liquid\TagBlock;
use Keepsuit\Liquid\Tokenizer;
use Keepsuit\Liquid\TokenType;

class ForTag extends TagBlock implements HasParseTreeVisitorChildren
{
    use ParserSwitching;

    const Syntax = '/\A('.Regex::VariableSegment.'+)\s+in\s+('.Regex::QuotedFragment.'+)\s*(reversed)?/';

    protected string $variableName;

    protected mixed $collectionName;

    protected mixed $from = null;

    protected mixed $limit = null;

    protected BlockBodySection $forBlock;

    protected ?BlockBodySection $elseBlock = null;

    public static function tagName(): string
    {
        return 'for';
    }

    public function parse(Tokenizer $tokenizer): static
    {
        parent::parse($tokenizer);

        $this->forBlock = $this->bodySections[0];

        if (count($this->bodySections) > 1) {
            $this->elseBlock = $this->bodySections[1];
        }

        $this->strictParseWithErrorModeFallback($this->forBlock->startDelimiter()->markup, $this->parseContext);

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
        // TODO: Implement laxParse() method.
        throw new \RuntimeException('Not implemented yet.');
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
