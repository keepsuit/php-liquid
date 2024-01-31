<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Condition\Condition;
use Keepsuit\Liquid\Condition\ElseCondition;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Nodes\BodyNode;
use Keepsuit\Liquid\Nodes\TagParseContext;
use Keepsuit\Liquid\Parse\ExpressionParser;
use Keepsuit\Liquid\Parse\TokenType;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\TagBlock;

/**
 * @phpstan-import-type Expression from ExpressionParser
 */
class CaseTag extends TagBlock
{
    /** @var Condition[] */
    protected array $conditions = [];

    /**
     * @var Expression
     */
    protected mixed $left = null;

    public static function tagName(): string
    {
        return 'case';
    }

    public function parse(TagParseContext $context): static
    {
        if ($context->tag === 'case') {
            $this->left = $context->params->expression();
        } else {
            $this->conditions[] = $this->mapBodySectionToCondition($context);
        }

        return $this;
    }

    public function render(RenderContext $context): string
    {
        foreach ($this->conditions as $condition) {
            if ($condition->else()) {
                return $condition->body?->render($context) ?? '';
            }

            if ($condition->evaluate($context)) {
                return $condition->body?->render($context) ?? '';
            }
        }

        return '';
    }

    public function children(): array
    {
        return array_filter(
            array_map(fn (Condition $block) => $block->body, $this->conditions),
            fn (?BodyNode $block) => $block !== null
        );
    }

    public function parseTreeVisitorChildren(): array
    {
        return [$this->left, ...$this->conditions];
    }

    public function blank(): bool
    {
        foreach ($this->conditions as $condition) {
            if (! $condition->body?->blank()) {
                return false;
            }
        }

        return true;
    }

    protected function mapBodySectionToCondition(TagParseContext $bodySection): Condition
    {
        $condition = match ($bodySection->tag) {
            'when' => $this->recordWhenCondition($bodySection),
            'else' => $this->recordElseCondition($bodySection),
            default => throw new SyntaxException('Unknown tag '.$bodySection->tag)
        };

        if ($bodySection->body?->blank()) {
            $bodySection->body->removeBlankStrings();
        }

        $condition->body($bodySection->body);

        $bodySection->params->assertEnd();

        return $condition;
    }

    protected function recordWhenCondition(TagParseContext $bodySection): Condition
    {
        $condition = new Condition($this->left, '==', $bodySection->params->expression());

        if ($bodySection->params->idOrFalse('or') || $bodySection->params->consumeOrFalse(TokenType::Comma)) {
            $condition->or($this->recordWhenCondition($bodySection));
        }

        $bodySection->params->assertEnd();

        return $condition;
    }

    protected function recordElseCondition(TagParseContext $bodySection): Condition
    {
        $bodySection->params->assertEnd();

        return new ElseCondition();
    }

    public function isSubTag(string $tagName): bool
    {
        return in_array($tagName, ['when', 'else']);
    }
}
