<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Block;
use Keepsuit\Liquid\Condition;
use Keepsuit\Liquid\ElseCondition;
use Keepsuit\Liquid\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\ParseContext;
use Keepsuit\Liquid\Parser;
use Keepsuit\Liquid\ParserSwitching;
use Keepsuit\Liquid\TokenType;

class IfTag extends Block implements HasParseTreeVisitorChildren
{
    use ParserSwitching;

    protected array $blocks = [];

    public function __construct(string $tagName, string $markup, ParseContext $parseContext)
    {
        parent::__construct($tagName, $markup, $parseContext);

        $this->pushBlock('if', $markup);
    }

    public static function name(): string
    {
        return 'if';
    }

    public function parseTreeVisitorChildren(): array
    {
        return $this->blocks;
    }

    protected function pushBlock(string $tag, string $markup): void
    {
        $block = match (true) {
            $tag === 'else' => new ElseCondition(),
            default => $this->strictParseWithErrorModeFallback($markup, $this->parseContext),
        };

        $this->blocks[] = $block;
        // $block->attach($newBody);
    }

    protected function strictParse(string $markup): mixed
    {
        $parser = new Parser($markup);

        $condition = $this->parseBinaryComparison($parser);
        $parser->consume(TokenType::EndOfString);

        return $condition;
    }

    protected function laxParse(string $markup): mixed
    {
        throw new \RuntimeException('Not implemented');
    }

    protected function parseBinaryComparison(Parser $parser): Condition
    {
        $condition = $this->parseComparison($parser);
        $firstCondition = $condition;
        while ($operator = ($parser->idOrFalse('and') || $parser->idOrFalse('or'))) {
            $childCondition = $this->parseComparison($parser);
            dd('parseBinaryComparison', $childCondition);
        }

        return $firstCondition;
    }

    protected function parseComparison(Parser $parser): Condition
    {
        $a = $this->parseExpression($parser->expression());

        if ($operator = $parser->consumeOrFalse(TokenType::Comparison)) {
            $b = $this->parseExpression($parser->expression());

            return new Condition($a, $operator, $b);
        } else {
            return new Condition($a);
        }
    }

    protected function parseExpression(string $markup): mixed
    {
        return $this->parseContext->parseExpression($markup);
        //        return Condition::parseExpression($markup, $this->parseContext);
    }
}
