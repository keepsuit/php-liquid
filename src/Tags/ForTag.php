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

    public function __construct(string $tagName, string $markup, ParseContext $parseContext)
    {
        parent::__construct($tagName, $markup, $parseContext);

        $this->strictParseWithErrorModeFallback($markup, $parseContext);
    }

    public static function name(): string
    {
        return 'for';
    }

    public function parse(Tokenizer $tokenizer): static
    {
        $this->forBlock = $this->parseBody($tokenizer);

        if (! $this->forBlock->blank) {
            $this->elseBlock = $this->parseBody($tokenizer);
        }

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

        if ($parser->idOrFalse('in') === null) {
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

    /**
     * @throws SyntaxException
     */
    protected function unknownTagHandler(string $tagName, string $markup): bool
    {
        dd('unknownTagHandler', $tagName, $markup);

        return parent::unknownTagHandler($tagName, $markup);
    }

    public function parseTreeVisitorChildren(): array
    {
        $nodeList = $this->elseBlock ? [$this->forBlock, $this->elseBlock] : [$this->forBlock];

        return Arr::compact([
            ...$nodeList,
            $this->limit,
            $this->from,
            $this->collectionName,
        ]);
    }
}
