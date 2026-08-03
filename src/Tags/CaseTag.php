<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Compiler\CompilerContext;
use Keepsuit\Liquid\Condition\Condition;
use Keepsuit\Liquid\Condition\ElseCondition;
use Keepsuit\Liquid\Contracts\CanBeCompiled;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Nodes\BodyNode;
use Keepsuit\Liquid\Parse\ExpressionParser;
use Keepsuit\Liquid\Parse\TagParseContext;
use Keepsuit\Liquid\Parse\TokenType;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\TagBlock;

/**
 * @phpstan-import-type Expression from ExpressionParser
 */
class CaseTag extends TagBlock implements CanBeCompiled
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
        try {
            if ($context->tag === 'case') {
                $this->left = $context->params->expression();
                $context->params->assertEnd();
            } else {
                $this->conditions[] = $this->mapBodySectionToCondition($context);
            }
        } catch (SyntaxException $e) {
            throw SyntaxException::tagSyntaxException(static::tagName(), match ($context->tag) {
                'case' => 'case <expression>',
                'when' => 'when <expression> [, <expression>...]',
                'else' => 'else',
                default => ''
            }, $e);
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

    public function compile(CompilerContext $context): void
    {
        $first = true;

        foreach ($this->conditions as $condition) {
            $isElse = $condition->else();

            if ($isElse && $first) {
                if ($condition->body !== null) {
                    $body = $context->compileBodyToMethod($condition->body);
                    $context->write('yield from $this->'.$body.'($context);');
                }

                break;
            }

            if ($isElse) {
                $context->write('else {');
            } else {
                $keyword = $first ? 'if' : 'elseif';
                $conditionValue = $context->writeRuntimeValue($condition);
                $context->write($keyword.' ('.$conditionValue.'->evaluate($context)) {');
            }

            $context->indent();

            if ($condition->body !== null) {
                $body = $context->compileBodyToMethod($condition->body);
                $context->write('yield from $this->'.$body.'($context);');
            }

            $context->outdent()->write('}');

            if ($isElse) {
                break;
            }

            $first = false;
        }
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

    /**
     * @throws SyntaxException
     */
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

    /**
     * @throws SyntaxException
     */
    protected function recordWhenCondition(TagParseContext $bodySection): Condition
    {
        if ($bodySection->params->isEnd()) {
            throw SyntaxException::unexpectedEndOfTemplate();
        }

        $condition = new Condition($this->left, '==', $bodySection->params->expression());

        if ($bodySection->params->idOrFalse('or') || $bodySection->params->consumeOrFalse(TokenType::Comma)) {
            $condition->or($this->recordWhenCondition($bodySection));
        }

        $bodySection->params->assertEnd();

        return $condition;
    }

    /**
     * @throws SyntaxException
     */
    protected function recordElseCondition(TagParseContext $bodySection): Condition
    {
        $bodySection->params->assertEnd();

        return new ElseCondition;
    }

    public function isSubTag(string $tagName): bool
    {
        return in_array($tagName, ['when', 'else'], true);
    }
}
