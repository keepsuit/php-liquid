<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\BlockBodySection;
use Keepsuit\Liquid\Condition;
use Keepsuit\Liquid\Context;
use Keepsuit\Liquid\ElseCondition;
use Keepsuit\Liquid\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Regex;
use Keepsuit\Liquid\SyntaxException;
use Keepsuit\Liquid\TagBlock;
use Keepsuit\Liquid\Tokenizer;

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

    public function parse(Tokenizer $tokenizer): static
    {
        parent::parse($tokenizer);
        $caseSection = array_shift($this->bodySections);

        if (preg_match(self::Syntax, $this->markup, $matches) === 1) {
            $this->left = $this->parseExpression($matches[1]);
        } else {
            throw new SyntaxException($this->parseContext->locale->translate('errors.syntax.case'));
        }

        $this->conditions = array_map(fn (BlockBodySection $block) => $this->parseBodySection($block), $this->bodySections);

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

    protected function parseBodySection(BlockBodySection $section): Condition
    {
        assert($section->startDelimiter() !== null);

        $condition = match ($section->startDelimiter()->tag) {
            'when' => $this->recordWhenCondition($section->startDelimiter()->markup),
            'else' => $this->recordElseCondition($section->startDelimiter()->markup),
            default => SyntaxException::unknownTag($this->parseContext, $section->startDelimiter()->tag, $section->startDelimiter()->markup),
        };

        assert($condition instanceof Condition);

        $condition->attach($section);

        return $condition;
    }

    protected function recordWhenCondition(string $markup): Condition
    {
        if (preg_match(self::WhenSyntax, $markup, $matches) !== 1) {
            throw new SyntaxException($this->parseContext->locale->translate('errors.syntax.case_invalid_when'));
        }

        $condition = new Condition($this->left, '==', $this->parseExpression($matches[1]));

        if ($matches[2] ?? false) {
            $condition->or($this->recordWhenCondition($matches[2]));
        }

        return $condition;
    }

    protected function recordElseCondition(string $markup): Condition
    {
        if (trim($markup) !== '') {
            throw new SyntaxException($this->parseContext->locale->translate('errors.syntax.case_invalid_else'));
        }

        return new ElseCondition();
    }

    protected function isSubTag(string $tagName): bool
    {
        return in_array($tagName, ['when', 'else']);
    }
}
