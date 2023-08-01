<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Block;
use Keepsuit\Liquid\Condition;
use Keepsuit\Liquid\ElseCondition;
use Keepsuit\Liquid\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\ParseContext;
use Keepsuit\Liquid\Regex;
use Keepsuit\Liquid\SyntaxException;
use Keepsuit\Liquid\Tokenizer;

class CaseTag extends Block implements HasParseTreeVisitorChildren
{
    protected const Syntax = '/('.Regex::QuotedFragment.')/';

    protected const WhenSyntax = '/('.Regex::QuotedFragment.')(?:(?:\s+or\s+|\s*\,\s*)(.*))?/m';

    /** @var Condition[] */
    protected array $blocks = [];

    protected mixed $left = null;

    public function __construct(string $markup, ParseContext $parseContext)
    {
        parent::__construct($markup, $parseContext);

        if (preg_match(self::Syntax, $markup, $matches) === 1) {
            $this->left = $this->parseExpression($matches[1]);
        } else {
            throw new SyntaxException($parseContext->locale->translate('errors.syntax.case'));
        }
    }

    public static function tagName(): string
    {
        return 'case';
    }

    public function parse(Tokenizer $tokenizer): static
    {
        $caseBody = $this->parseBody($tokenizer);

        if (count($this->blocks) > 0) {
            $this->blocks[count($this->blocks) - 1]->attach($caseBody);
        }

        foreach (array_reverse($this->blocks) as $block) {
            if ($block->attachment?->blank) {
                $block->attachment->removeBlankStrings();
            }
        }

        return $this;
    }

    protected function recordWhenCondition(string $markup): void
    {
        while ($markup !== '') {
            $matchesCount = preg_match_all(self::WhenSyntax, $markup, $matches);

            if ($matchesCount === false || $matchesCount === 0) {
                throw new SyntaxException($this->parseContext->locale->translate('errors.syntax.case_invalid_when'));
            }

            $markup = $matches[2][0] ?? '';
            $block = new Condition($this->left, '==', $this->parseExpression($matches[1][0]));
            $this->blocks[] = $block;
        }
    }

    protected function recordElseCondition(string $markup): void
    {
        if (trim($markup) !== '') {
            throw new SyntaxException($this->parseContext->locale->translate('errors.syntax.case_invalid_else'));
        }

        $block = new ElseCondition();
        $this->blocks[] = $block;
    }

    /**
     * @throws SyntaxException
     */
    protected function unknownTagHandler(string $tagName, string $markup): bool
    {
        if ($tagName === 'when') {
            $this->recordWhenCondition($markup);

            return true;
        }

        if ($tagName === 'else') {
            $this->recordElseCondition($markup);

            return true;
        }

        return parent::unknownTagHandler($tagName, $markup);
    }

    public function parseTreeVisitorChildren(): array
    {
        return [$this->left, ...$this->blocks];
    }
}
