<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Condition\Condition;
use Keepsuit\Liquid\Condition\ElseCondition;
use Keepsuit\Liquid\Contracts\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Nodes\BlockBodySection;
use Keepsuit\Liquid\Parse\Parser;
use Keepsuit\Liquid\Parse\ParserSwitching;
use Keepsuit\Liquid\Parse\Tokenizer;
use Keepsuit\Liquid\Parse\TokenType;
use Keepsuit\Liquid\Render\Context;
use Keepsuit\Liquid\TagBlock;

class IfTag extends TagBlock implements HasParseTreeVisitorChildren
{
    use ParserSwitching;

    /** @var Condition[] */
    protected array $conditions = [];

    public static function tagName(): string
    {
        return 'if';
    }

    public function parse(Tokenizer $tokenizer): static
    {
        parent::parse($tokenizer);

        $this->conditions = array_map(fn (BlockBodySection $block) => $this->parseBodySection($block), $this->bodySections);

        return $this;
    }

    public function nodeList(): array
    {
        return array_map(fn (Condition $block) => $block->attachment, $this->conditions);
    }

    protected function isSubTag(string $tagName): bool
    {
        return in_array($tagName, ['else', 'elsif'], true);
    }

    protected function parseBodySection(BlockBodySection $section): Condition
    {
        assert($section->startDelimiter() !== null);

        $condition = match (true) {
            $section->startDelimiter()->tag === 'else' => new ElseCondition(),
            default => $this->strictParseWithErrorModeFallback($section->startDelimiter()->markup, $this->parseContext),
        };

        assert($condition instanceof Condition);

        if ($section->blank()) {
            $section->removeBlankStrings();
        }
        $condition->attach($section);

        return $condition;
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

    public function parseTreeVisitorChildren(): array
    {
        return $this->conditions;
    }

    public function render(Context $context): string
    {
        $output = '';
        foreach ($this->conditions as $condition) {
            $result = $condition->evaluate($context);

            if ($result) {
                return $condition->attachment?->render($context) ?? '';
            }
        }

        return $output;
    }
}
