<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Compiler\CompilerContext;
use Keepsuit\Liquid\Condition\Condition;
use Keepsuit\Liquid\Condition\ElseCondition;
use Keepsuit\Liquid\Contracts\CanBeCompiled;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Parse\TagParseContext;
use Keepsuit\Liquid\Parse\TokenType;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Support\Arr;
use Keepsuit\Liquid\TagBlock;

class IfTag extends TagBlock implements CanBeCompiled
{
    /** @var Condition[] */
    protected array $conditions = [];

    public static function tagName(): string
    {
        return 'if';
    }

    public function parse(TagParseContext $context): static
    {
        try {
            $this->conditions[] = $this->mapBodySectionToCondition($context);
        } catch (SyntaxException $e) {
            throw SyntaxException::tagSyntaxException(static::tagName(), match ($context->tag) {
                'if' => 'if <condition>',
                'elsif' => 'elsif <condition>',
                'else' => 'else',
                default => ''
            }, $e);
        }

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

    public function compile(CompilerContext $context): void
    {
        $this->compileConditions($context, $this->conditions);
    }

    /**
     * @param  array<Condition>  $conditions
     */
    protected function compileConditions(CompilerContext $context, array $conditions, bool $first = true): void
    {
        foreach ($conditions as $condition) {
            if ($condition->else()) {
                $context->write('else {');
            } else {
                $keyword = $first ? 'if' : 'elseif';
                $conditionValue = $context->writeRuntimeValue($condition);
                $context->write($keyword.' ('.$conditionValue.'->evaluate($context)) {');
            }

            $context->indent();

            if ($condition->body !== null) {
                $context->subcompile($condition->body);
            }

            $context->outdent()->write('}');
            $first = false;
        }
    }

    public function parseTreeVisitorChildren(): array
    {
        return $this->conditions;
    }

    /**
     * @throws SyntaxException
     */
    protected function mapBodySectionToCondition(TagParseContext $bodySection): Condition
    {
        $condition = match ($bodySection->tag) {
            'else' => new ElseCondition,
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

    /**
     * @throws SyntaxException
     */
    protected function parseCondition(TagParseContext $bodySection): Condition
    {
        return $this->parseBinaryComparison($bodySection);
    }

    /**
     * @throws SyntaxException
     */
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

    /**
     * @throws SyntaxException
     */
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
