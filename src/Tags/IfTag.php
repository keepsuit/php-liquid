<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Condition\Condition;
use Keepsuit\Liquid\Condition\ElseCondition;
use Keepsuit\Liquid\Contracts\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Nodes\BlockBodySection;
use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\Parse\Parser;
use Keepsuit\Liquid\Parse\Tokenizer;
use Keepsuit\Liquid\Parse\TokenType;
use Keepsuit\Liquid\Render\Context;
use Keepsuit\Liquid\TagBlock;

class IfTag extends TagBlock implements HasParseTreeVisitorChildren
{
    /** @var Condition[] */
    protected array $conditions = [];

    public static function tagName(): string
    {
        return 'if';
    }

    public function parse(ParseContext $parseContext, Tokenizer $tokenizer): static
    {
        parent::parse($parseContext, $tokenizer);

        try {
            $this->conditions = array_map(fn (BlockBodySection $block) => $this->parseBodySection($parseContext, $block), $this->bodySections);
        } catch (SyntaxException $exception) {
            $exception->markupContext = $this->markup;
            throw $exception;
        }

        return $this;
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

    public function parseTreeVisitorChildren(): array
    {
        return $this->conditions;
    }

    public function nodeList(): array
    {
        return array_map(fn (Condition $block) => $block->attachment, $this->conditions);
    }

    protected function isSubTag(string $tagName): bool
    {
        return in_array($tagName, ['else', 'elsif'], true);
    }

    /**
     * @throws SyntaxException
     */
    protected function parseBodySection(ParseContext $parseContext, BlockBodySection $section): Condition
    {
        assert($section->startDelimiter() !== null);

        $condition = match (true) {
            $section->startDelimiter()->tag === 'else' => new ElseCondition(),
            default => $this->parseCondition($parseContext, $section->startDelimiter()->markup),
        };

        if ($section->blank()) {
            $section->removeBlankStrings();
        }
        $condition->attach($section);

        return $condition;
    }

    /**
     * @throws SyntaxException
     */
    protected function parseCondition(ParseContext $parseContext, string $markup): Condition
    {
        $parser = new Parser($markup);

        $condition = $this->parseBinaryComparison($parseContext, $parser);
        $parser->consume(TokenType::EndOfString);

        return $condition;
    }

    protected function parseBinaryComparison(ParseContext $parseContext, Parser $parser): Condition
    {
        $condition = $this->parseComparison($parseContext, $parser);
        $firstCondition = $condition;

        while ($operator = $parser->idOrFalse('and') ?: $parser->idOrFalse('or')) {
            $childCondition = $this->parseComparison($parseContext, $parser);
            $condition->{$operator}($childCondition);
            $condition = $childCondition;
        }

        return $firstCondition;
    }

    protected function parseComparison(ParseContext $parseContext, Parser $parser): Condition
    {
        $a = $this->parseExpression($parseContext, $parser->expression());

        if ($operator = $parser->consumeOrFalse(TokenType::Comparison)) {
            $b = $this->parseExpression($parseContext, $parser->expression());

            return new Condition($a, $operator, $b);
        } else {
            return new Condition($a);
        }
    }
}
