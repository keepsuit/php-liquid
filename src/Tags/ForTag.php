<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Contracts\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Drops\ForLoopDrop;
use Keepsuit\Liquid\Exceptions\InvalidArgumentException;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Interrupts\BreakInterrupt;
use Keepsuit\Liquid\Nodes\BodyNode;
use Keepsuit\Liquid\Nodes\Range;
use Keepsuit\Liquid\Nodes\RangeLookup;
use Keepsuit\Liquid\Nodes\TagParseContext;
use Keepsuit\Liquid\Nodes\VariableLookup;
use Keepsuit\Liquid\Parse\ExpressionParser;
use Keepsuit\Liquid\Parse\TokenStream;
use Keepsuit\Liquid\Parse\TokenType;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Support\Arr;
use Keepsuit\Liquid\TagBlock;
use Traversable;

/**
 * @phpstan-import-type Expression from ExpressionParser
 */
class ForTag extends TagBlock implements HasParseTreeVisitorChildren
{
    protected string $variableName;

    protected VariableLookup|RangeLookup|string $collection;

    protected string $name;

    protected bool $reversed;

    /**
     * @var Expression|null
     */
    protected mixed $from = null;

    /**
     * @var Expression|null
     */
    protected mixed $limit = null;

    protected BodyNode $forBlock;

    protected ?BodyNode $elseBlock = null;

    public static function tagName(): string
    {
        return 'for';
    }

    public function parse(TagParseContext $context): static
    {
        match ($context->tag) {
            'for' => $this->parseForBlock($context),
            'else' => $this->parseElseBlock($context),
            default => throw new SyntaxException('Invalid tag'),
        };

        return $this;
    }

    public function render(RenderContext $context): string
    {
        $segment = $this->collectionSegment($context);

        if ($segment === []) {
            return $this->renderElse($context);
        }

        return $this->renderSegment($context, $segment);
    }

    public function children(): array
    {
        return $this->elseBlock ? [$this->forBlock, $this->elseBlock] : [$this->forBlock];
    }

    public function parseTreeVisitorChildren(): array
    {
        return Arr::compact([
            ...$this->children(),
            $this->limit,
            $this->from,
            $this->collection,
        ]);
    }

    public function blank(): bool
    {
        return $this->forBlock->blank() && ($this->elseBlock?->blank() ?? true);
    }

    protected function setAttribute(string $attribute, TokenStream $tokenStream): void
    {
        if ($attribute === 'offset') {
            $expression = $tokenStream->expression();
            $this->from = match (true) {
                $expression instanceof VariableLookup, is_string($expression) => (string) $expression === 'continue' ? 'continue' : $expression,
                default => $expression,
            };

            return;
        }

        if ($attribute === 'limit') {
            $this->limit = $tokenStream->expression();

            return;
        }
    }

    public function isSubTag(string $tagName): bool
    {
        return in_array($tagName, ['else'], true);
    }

    protected function collectionSegment(RenderContext $context): array
    {
        $offsets = $context->getRegister('for') ?? [];
        assert(is_array($offsets));

        $collection = $context->evaluate($this->collection) ?? [];
        $collection = match (true) {
            $collection instanceof Range => $collection->toArray(),
            $collection instanceof Traversable => iterator_to_array($collection),
            is_iterable($collection) => (array) $collection,
            default => $collection,
        };
        assert(is_array($collection));

        if ($this->from === 'continue') {
            $offset = $offsets[$this->name];
        } else {
            $fromValue = $context->evaluate($this->from);
            $offset = match (true) {
                $fromValue === null => 0,
                is_numeric($fromValue) => (int) $fromValue,
                default => throw new InvalidArgumentException('Invalid integer'),
            };
        }
        assert(is_int($offset));

        $limitValue = $context->evaluate($this->limit);
        $length = $limitValue === null ? null : (is_numeric($limitValue) ? (int) $limitValue : throw new InvalidArgumentException('Invalid integer'));
        $segment = array_slice($collection, $offset, $length);
        $segment = $this->reversed ? array_reverse($segment) : $segment;

        $offsets[$this->name] = $offset + count($segment);
        $context->setRegister('for', $offsets);

        return $segment;
    }

    protected function renderSegment(RenderContext $context, array $segment): string
    {
        /** @var ForLoopDrop[] $forStack */
        $forStack = $context->getRegister('for_stack') ?? [];
        assert(is_array($forStack));

        return $context->stack(function () use ($context, $segment, $forStack) {
            $loopVars = new ForLoopDrop(
                name: $this->name,
                length: count($segment),
                parentLoop: $forStack !== [] ? $forStack[count($forStack) - 1] : null,
            );

            $forStack[] = $loopVars;
            $context->setRegister('for_stack', $forStack);

            try {
                $context->set('forloop', $loopVars);
                $output = '';
                foreach ($segment as $value) {
                    $context->set($this->variableName, $value);
                    $output .= $this->forBlock->render($context);
                    $loopVars->increment();

                    $interrupt = $context->popInterrupt();

                    if ($interrupt instanceof BreakInterrupt) {
                        break;
                    }
                }
            } finally {
                $forStack = $context->getRegister('for_stack');
                assert(is_array($forStack));
                array_pop($forStack);
                $context->setRegister('for_stack', $forStack);
            }

            return $output;
        });
    }

    protected function renderElse(RenderContext $context): string
    {
        return $this->elseBlock?->render($context) ?? '';
    }

    protected function parseForBlock(TagParseContext $context): void
    {
        assert($context->body !== null);
        $this->forBlock = $context->body;

        $variableName = $context->params->expression();
        $this->variableName = match (true) {
            $variableName instanceof VariableLookup, is_string($variableName) => (string) $variableName,
            default => throw new SyntaxException('Invalid variable name'),
        };

        if (! $context->params->idOrFalse('in')) {
            throw new SyntaxException($context->getParseContext()->locale->translate('errors.syntax.for_invalid_in'));
        }

        $collection = $context->params->expression();
        $this->collection = match (true) {
            $collection instanceof VariableLookup, $collection instanceof RangeLookup, is_string($collection) => $collection,
            default => throw new SyntaxException('Invalid collection'),
        };

        $this->name = sprintf('%s-%s', $this->variableName, $this->collection);
        $this->reversed = $context->params->idOrFalse('reversed') !== false;

        while ($context->params->look(TokenType::Comma) || $context->params->look(TokenType::Identifier)) {
            $context->params->consumeOrFalse(TokenType::Comma);

            $attribute = $context->params->idOrFalse('limit') ?: $context->params->idOrFalse('offset');

            if (! $attribute) {
                throw new SyntaxException($context->getParseContext()->locale->translate('errors.syntax.for_invalid_attribute'));
            }

            $context->params->consume(TokenType::Colon);

            $this->setAttribute($attribute->data, $context->params);
        }

        $context->params->assertEnd();

        if ($this->forBlock->blank()) {
            $this->forBlock->removeBlankStrings();
        }
    }

    protected function parseElseBlock(TagParseContext $context): void
    {
        $this->elseBlock = $context->body;
        $context->params->assertEnd();

        if ($this->elseBlock?->blank()) {
            $this->elseBlock->removeBlankStrings();
        }
    }
}
