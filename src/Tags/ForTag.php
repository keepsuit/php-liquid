<?php

namespace Keepsuit\Liquid\Tags;

use Closure;
use Keepsuit\Liquid\Compiler\CompilerContext;
use Keepsuit\Liquid\Contracts\CanBeCompiled;
use Keepsuit\Liquid\Contracts\CanBeStreamed;
use Keepsuit\Liquid\Contracts\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Drops\ForLoopDrop;
use Keepsuit\Liquid\Exceptions\InvalidArgumentException;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Interrupts\BreakInterrupt;
use Keepsuit\Liquid\Nodes\BodyNode;
use Keepsuit\Liquid\Nodes\Literal;
use Keepsuit\Liquid\Nodes\Range;
use Keepsuit\Liquid\Nodes\VariableLookup;
use Keepsuit\Liquid\Parse\ExpressionParser;
use Keepsuit\Liquid\Parse\TagParseContext;
use Keepsuit\Liquid\Parse\TokenStream;
use Keepsuit\Liquid\Parse\TokenType;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Support\Arr;
use Keepsuit\Liquid\TagBlock;

/**
 * @phpstan-import-type Expression from ExpressionParser
 */
class ForTag extends TagBlock implements CanBeCompiled, CanBeStreamed, HasParseTreeVisitorChildren
{
    protected string $variableName;

    /**
     * @var Expression
     */
    protected mixed $collection;

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
        try {
            match ($context->tag) {
                'for' => $this->parseForBlock($context),
                'else' => $this->parseElseBlock($context),
                default => throw new SyntaxException('Invalid tag'),
            };
        } catch (SyntaxException $e) {
            throw SyntaxException::tagSyntaxException(static::tagName(), match ($context->tag) {
                'for' => 'for <var> in <collection> [attributes...]',
                'else' => 'else',
                default => ''
            }, $e);
        }

        return $this;
    }

    public function render(RenderContext $context): string
    {
        return $this->renderBlocks($context);
    }

    /**
     * Emit the loop bodies in the caller's frame. Collection and scope policies
     * remain shared runtime helpers; each iteration needs no callback or generator.
     */
    public function compile(CompilerContext $context): void
    {
        if (static::class !== self::class) {
            $context->compileFallback($this);

            return;
        }

        $segment = $context->temporaryVariable();
        $loop = $context->temporaryVariable();
        $value = $context->temporaryVariable();
        $context->write($segment.' = \\'.self::class.'::collectionSegmentFor($context, '
            .$context->writeCachedValue($this->collection).', '
            .$context->writeCachedValue($this->from).', '
            .$context->writeCachedValue($this->limit).', '
            .$context->writeValue($this->reversed).', '.$context->writeValue($this->name).');');
        $context->write('if ('.$segment.' !== []) {')->indent();
        $context->write($loop.' = \\'.self::class.'::enterLoop($context, '.$context->writeValue($this->name).', count('.$segment.'));');
        $context->write('try {')->indent();
        $context->write('foreach ('.$segment.' as '.$value.') {')->indent();
        $context->write('$context->set('.$context->writeValue($this->variableName).', '.$value.');');
        $context->compileBody($this->forBlock);
        $context->write($loop.'->increment();');
        $context->write('if ($context->popInterrupt() instanceof \\Keepsuit\\Liquid\\Interrupts\\BreakInterrupt) {')->indent();
        $context->write('break;')->outdent()->write('}');
        $context->outdent()->write('}');
        $context->outdent()->write('} finally {')->indent();
        $context->write('\\'.self::class.'::leaveLoop($context);');
        $context->outdent()->write('}');
        $context->outdent();
        if ($this->elseBlock !== null) {
            $context->write('} else {')->indent();
            $context->compileBody($this->elseBlock);
            $context->outdent();
        }
        $context->write('}');
    }

    /** @internal */
    public static function enterLoop(RenderContext $context, string $name, int $length): ForLoopDrop
    {
        /** @var ForLoopDrop[] $stack */
        $stack = $context->getRegister('for_stack') ?? [];
        assert(is_array($stack));
        $loop = new ForLoopDrop($name, $length, $stack !== [] ? $stack[count($stack) - 1] : null);
        $context->enterScope();
        $stack[] = $loop;
        $context->setRegister('for_stack', $stack);
        $context->set('forloop', $loop);

        return $loop;
    }

    /** @internal */
    public static function leaveLoop(RenderContext $context): void
    {
        try {
            $stack = $context->getRegister('for_stack');
            assert(is_array($stack));
            array_pop($stack);
            $context->setRegister('for_stack', $stack);
        } finally {
            $context->leaveScope();
        }
    }

    public function renderBlocks(RenderContext $context, ?Closure $forBody = null, ?Closure $elseBody = null): string
    {
        $segment = $this->collectionSegment($context);

        if ($segment === []) {
            return $elseBody !== null ? $elseBody($context) : $this->renderElse($context);
        }

        return $this->renderSegment($context, $segment, $forBody);
    }

    /**
     * @return \Generator<string>
     */
    public function stream(RenderContext $context): \Generator
    {
        yield from $this->streamBlocks($context);
    }

    /**
     * @param  (Closure(RenderContext): iterable<string>)|null  $forBody
     * @param  (Closure(RenderContext): iterable<string>)|null  $elseBody
     * @return \Generator<string>
     */
    public function streamBlocks(RenderContext $context, ?Closure $forBody = null, ?Closure $elseBody = null): \Generator
    {
        $segment = $this->collectionSegment($context);

        if ($segment === []) {
            if ($elseBody !== null) {
                yield from $elseBody($context);
            } elseif ($this->elseBlock !== null) {
                yield from $this->elseBlock->stream($context);
            }

            return;
        }

        yield from $this->streamSegment($context, $segment, $forBody);
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
        return self::collectionSegmentFor($context, $this->collection, $this->from, $this->limit, $this->reversed, $this->name);
    }

    /** @internal */
    public static function collectionSegmentFor(RenderContext $context, mixed $expression, mixed $from, mixed $limit, bool $reversed, string $name): array
    {
        $offsets = $context->getRegister('for') ?? [];
        assert(is_array($offsets));

        $collection = $context->evaluate($expression);
        if (! $collection instanceof Range || $collection::class !== Range::class) {
            $collection = Arr::fromCollection($collection);
        }

        if ($from === 'continue') {
            $offset = $offsets[$name] ?? 0;
        } else {
            $fromValue = $from === null ? null : $context->evaluate($from);
            $offset = match (true) {
                $fromValue === null => 0,
                is_numeric($fromValue) => (int) $fromValue,
                default => throw new InvalidArgumentException('Invalid integer'),
            };
        }
        assert(is_int($offset));

        $limitValue = $limit === null ? null : $context->evaluate($limit);
        $length = match (true) {
            $limitValue === null => null,
            is_numeric($limitValue) => (int) $limitValue,
            default => throw new InvalidArgumentException('Invalid integer'),
        };
        $segment = match (true) {
            $collection instanceof Range => $collection->slice($offset, $length),
            $offset === 0 && $length === null => $collection,
            default => array_slice($collection, $offset, $length)
        };
        $segment = $reversed ? array_reverse($segment) : $segment;

        $offsets[$name] = $offset + count($segment);
        $context->setRegister('for', $offsets);

        return $segment;
    }

    protected function renderSegment(RenderContext $context, array $segment, ?Closure $forBody = null): string
    {
        $loopVars = self::enterLoop($context, $this->name, count($segment));

        try {
            $output = '';
            foreach ($segment as $value) {
                $context->set($this->variableName, $value);
                $output .= $forBody !== null ? $forBody($context) : $this->forBlock->render($context);
                $loopVars->increment();

                if ($context->popInterrupt() instanceof BreakInterrupt) {
                    break;
                }
            }

            return $output;
        } finally {
            self::leaveLoop($context);
        }
    }

    /**
     * @param  (Closure(RenderContext): iterable<string>)|null  $forBody
     * @return \Generator<string>
     */
    protected function streamSegment(RenderContext $context, array $segment, ?Closure $forBody = null): \Generator
    {
        $loopVars = self::enterLoop($context, $this->name, count($segment));

        try {
            foreach ($segment as $value) {
                $context->set($this->variableName, $value);

                if ($forBody !== null) {
                    yield from $forBody($context);
                } else {
                    yield from $this->forBlock->stream($context);
                }

                $loopVars->increment();

                if ($context->popInterrupt() instanceof BreakInterrupt) {
                    break;
                }
            }
        } finally {
            self::leaveLoop($context);
        }
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
            throw new SyntaxException("For loops require an 'in' clause");
        }

        if ($context->params->isEnd()) {
            throw new SyntaxException('Invalid collection');
        }

        $this->collection = $context->params->expression();

        $this->name = sprintf('%s-%s', $this->variableName, match (true) {
            $this->collection instanceof Literal => $this->collection->value,
            $this->collection === null => 'nil',
            is_bool($this->collection) => $this->collection ? 'true' : 'false',
            is_string($this->collection) => sprintf("'%s'", $this->collection),
            default => (string) $this->collection,
        });
        $this->reversed = $context->params->idOrFalse('reversed') !== false;

        while ($context->params->look(TokenType::Comma) || $context->params->look(TokenType::Identifier)) {
            $context->params->consumeOrFalse(TokenType::Comma);

            $attribute = $context->params->idOrFalse('limit') ?: $context->params->idOrFalse('offset');

            if (! $attribute) {
                throw new SyntaxException('Invalid attribute in for loop. Valid attributes are limit and offset');
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
        if ($this->elseBlock !== null) {
            throw new SyntaxException('A for block can only contain one else tag.');
        }

        $this->elseBlock = $context->body;
        $context->params->assertEnd();

        if ($this->elseBlock?->blank()) {
            $this->elseBlock->removeBlankStrings();
        }
    }
}
