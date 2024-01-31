<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Condition\Condition;
use Keepsuit\Liquid\Condition\ElseCondition;
use Keepsuit\Liquid\Nodes\TagParseContext;
use Keepsuit\Liquid\Parse\TokenType;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Support\Arr;
use Keepsuit\Liquid\TagBlock;

class IfTag extends TagBlock
{
    /** @var Condition[] */
    protected array $conditions = [];

    public static function tagName(): string
    {
        return 'if';
    }

    public function parse(TagParseContext $context): static
    {
        $this->conditions[] = $this->mapBodySectionToCondition($context);

        return $this;
    }

    public function render(RenderContext $context): string
    {
        $output = '';
        foreach ($this->conditions as $condition) {
            $result = $condition->evaluate($context);

            if ($result) {
                return $condition->body?->render($context) ?? '';
            }
        }

        return $output;
    }

    public function parseTreeVisitorChildren(): array
    {
        return $this->conditions;
    }

    protected function mapBodySectionToCondition(TagParseContext $bodySection): Condition
    {
        $condition = match ($bodySection->tag) {
            'else' => new ElseCondition(),
            default => $this->parseCondition($bodySection)
        };

        if ($bodySection->body?->blank()) {
            $bodySection->body->removeBlankStrings();
        }

        $condition->body($bodySection->body);

        $bodySection->params->assertEnd();

        return $condition;
    }

    public function children(): array
    {
        return Arr::compact(array_map(fn (Condition $block) => $block->body, $this->conditions));
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

    public function isSubTag(string $tagName): bool
    {
        return in_array($tagName, ['else', 'elsif'], true);
    }

    protected function parseCondition(TagParseContext $bodySection): Condition
    {
        return $this->parseBinaryComparison($bodySection);
    }

    protected function parseBinaryComparison(TagParseContext $bodySection): Condition
    {
        $condition = $this->parseComparison($bodySection);
        $firstCondition = $condition;

        while ($operator = $bodySection->params->idOrFalse('and') ?: $bodySection->params->idOrFalse('or')) {
            $childCondition = $this->parseComparison($bodySection);
            $condition->{$operator->data}($childCondition);
            $condition = $childCondition;
        }

        return $firstCondition;
    }

    protected function parseComparison(TagParseContext $bodySection): Condition
    {
        $a = $bodySection->params->expression();

        if ($operator = $bodySection->params->consumeOrFalse(TokenType::Comparison)) {
            $b = $bodySection->params->expression();

            return new Condition($a, $operator->data, $b);
        } else {
            return new Condition($a);
        }
    }
}
