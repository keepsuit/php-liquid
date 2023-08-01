<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Block;
use Keepsuit\Liquid\Condition;
use Keepsuit\Liquid\ElseCondition;
use Keepsuit\Liquid\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\ParseContext;
use Keepsuit\Liquid\Parser;
use Keepsuit\Liquid\ParserSwitching;
use Keepsuit\Liquid\SyntaxException;
use Keepsuit\Liquid\Tokenizer;
use Keepsuit\Liquid\TokenType;

class IfTag extends Block implements HasParseTreeVisitorChildren
{
    use ParserSwitching;

    /** @var Condition[] */
    protected array $blocks = [];

    public function __construct(string $markup, ParseContext $parseContext)
    {
        parent::__construct($markup, $parseContext);

        $this->pushBlock('if', $markup);
    }

    public static function tagName(): string
    {
        return 'if';
    }

    public function parse(Tokenizer $tokenizer): static
    {
        $ifBody = $this->parseBody($tokenizer);

        if (count($this->blocks) > 0) {
            $this->blocks[count($this->blocks) - 1]->attach($ifBody);
        }

        foreach (array_reverse($this->blocks) as $block) {
            if ($block->attachment?->blank) {
                $block->attachment->removeBlankStrings();
            }
        }

        return $this;
    }

    protected function pushBlock(string $tag, string $markup): Condition
    {
        $block = match (true) {
            $tag === 'else' => new ElseCondition(),
            default => $this->strictParseWithErrorModeFallback($markup, $this->parseContext),
        };
        assert($block instanceof Condition);

        $this->blocks[] = $block;
        $block->attach($this->parseContext->newBlockBody());

        return $block;
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

        while ($operator = $parser->idOrFalse('and') ?: $parser->idOrFalse('or')) {
            $childCondition = $this->parseComparison($parser);
            $condition->{$operator}($childCondition);
            $condition = $childCondition;
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

    /**
     * @throws SyntaxException
     */
    protected function unknownTagHandler(string $tagName, string $markup): bool
    {
        if (in_array($tagName, ['elsif', 'else'])) {
            $this->pushBlock($tagName, $markup);

            return true;
        }

        return parent::unknownTagHandler($tagName, $markup);
    }

    public function parseTreeVisitorChildren(): array
    {
        return $this->blocks;
    }
}
