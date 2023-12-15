<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Condition\Condition;
use Keepsuit\Liquid\Condition\ElseCondition;
use Keepsuit\Liquid\Contracts\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Nodes\BlockBodySection;
use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\Parse\Parser;
use Keepsuit\Liquid\Parse\Regex;
use Keepsuit\Liquid\Parse\Tokenizer;
use Keepsuit\Liquid\Parse\TokenType;
use Keepsuit\Liquid\Render\Context;
use Keepsuit\Liquid\TagBlock;

class CaseTag extends TagBlock implements HasParseTreeVisitorChildren
{
    protected const Syntax = '/('.Regex::QuotedFragment.')/';

    protected const WhenSyntax = '/('.Regex::QuotedFragment.')(?:(?:\s+or\s+|\s*\,\s*)(.*))?/m';

    /** @var Condition[] */
    protected array $conditions = [];

    protected mixed $left = null;

    public static function tagName(): string
    {
        return 'case';
    }

    public function parse(ParseContext $parseContext, Tokenizer $tokenizer): static
    {
        parent::parse($parseContext, $tokenizer);

        $caseSection = array_shift($this->bodySections);

        $parser = $this->newParser();
        $condition = $parser->expression();
        $parser->consume(TokenType::EndOfString);

        $this->left = $this->parseExpression($condition);

        $this->conditions = array_map(fn (BlockBodySection $block) => $this->parseBodySection($parseContext, $block), $this->bodySections);

        return $this;
    }

    public function render(Context $context): string
    {
        foreach ($this->conditions as $condition) {
            if ($condition->else()) {
                return $condition->attachment?->render($context) ?? '';
            }

            if ($condition->evaluate($context)) {
                return $condition->attachment?->render($context) ?? '';
            }
        }

        return '';
    }

    public function nodeList(): array
    {
        return array_map(fn (Condition $block) => $block->attachment, $this->conditions);
    }

    public function parseTreeVisitorChildren(): array
    {
        return [$this->left, ...$this->conditions];
    }

    protected function parseBodySection(ParseContext $parseContext, BlockBodySection $section): Condition
    {
        assert($section->startDelimiter() !== null);

        $condition = match ($section->startDelimiter()->tag) {
            'when' => $this->recordWhenCondition($parseContext, $this->newParser($section->startDelimiter()->markup)),
            'else' => $this->recordElseCondition($parseContext, $section->startDelimiter()->markup),
            default => SyntaxException::unknownTag($parseContext, $section->startDelimiter()->tag, $section->startDelimiter()->markup),
        };

        assert($condition instanceof Condition);

        if ($section->blank()) {
            $section->removeBlankStrings();
        }
        $condition->attach($section);

        return $condition;
    }

    protected function recordWhenCondition(ParseContext $parseContext, Parser $parser): Condition
    {
        $expression = $parser->expression();

        $condition = new Condition($this->left, '==', $this->parseExpression($expression));

        while ($parser->idOrFalse('or') || $parser->consumeOrFalse(TokenType::Comma)) {
            $condition->or($this->recordWhenCondition($parseContext, $parser));
        }

        return $condition;
    }

    protected function recordElseCondition(ParseContext $parseContext, string $markup): Condition
    {
        if (trim($markup) !== '') {
            throw new SyntaxException($parseContext->locale->translate('errors.syntax.case_invalid_else'));
        }

        return new ElseCondition();
    }

    protected function isSubTag(string $tagName): bool
    {
        return in_array($tagName, ['when', 'else']);
    }
}
