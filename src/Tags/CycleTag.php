<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Compiler\CompilerContext;
use Keepsuit\Liquid\Contracts\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Exceptions\InvalidArgumentException;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Nodes\Variable;
use Keepsuit\Liquid\Nodes\VariableLookup;
use Keepsuit\Liquid\Parse\ExpressionParser;
use Keepsuit\Liquid\Parse\TagParseContext;
use Keepsuit\Liquid\Parse\TokenType;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Support\UndefinedVariable;
use Keepsuit\Liquid\Tag;

/**
 * @phpstan-import-type Expression from ExpressionParser
 */
class CycleTag extends Tag implements HasParseTreeVisitorChildren
{
    /**
     * @var list<Expression>
     */
    protected array $variables = [];

    protected string|int|float|VariableLookup|null $name = null;

    public static function tagName(): string
    {
        return 'cycle';
    }

    public function parse(TagParseContext $context): static
    {
        try {
            $this->name = null;
            $this->variables = [];

            if ($context->params->current() === null) {
                throw SyntaxException::unexpectedEndOfTemplate();
            }

            $first = $context->params->expression();
            if ($context->params->consumeOrFalse(TokenType::Colon)) {
                $this->name = match (true) {
                    is_string($first), is_int($first), is_float($first), $first instanceof VariableLookup => $first,
                    default => throw new SyntaxException('Invalid cycle name'),
                };
            } else {
                $this->variables[] = $first;
            }

            while ($this->variables === [] || $context->params->consumeOrFalse(TokenType::Comma)) {
                if ($context->params->isEnd()) {
                    throw SyntaxException::unexpectedEndOfTemplate();
                }

                $this->variables[] = $context->params->expression();
            }

            $hasLookups = array_filter($this->variables, fn (mixed $value) => $value instanceof VariableLookup) !== [];
            if ($this->name === null && ! $hasLookups) {
                $this->name = json_encode($this->variables, JSON_THROW_ON_ERROR);
            }

            $context->params->assertEnd();
        } catch (SyntaxException $e) {
            throw SyntaxException::tagSyntaxException(static::tagName(), 'cycle [<name>:] <value>[, <value>...]', $e);
        }

        return $this;
    }

    public function render(RenderContext $context): string
    {
        return self::renderValues($context, $this->name ?? sprintf('cycle:%d', spl_object_id($this)), $this->variables);
    }

    /** @internal */
    public function compileNative(CompilerContext $context): void
    {
        // Unnamed dynamic cycles are keyed by the node's runtime identity.
        if ($this->name === null) {
            $context->compileFallback($this);

            return;
        }

        $context->writeOutput('\\'.self::class.'::renderValues($context, '
            .$context->writeCachedValue($this->name).', '.$context->writeCachedValue($this->variables).')');
    }

    /**
     * @internal
     *
     * @param  list<Expression>  $variables
     */
    public static function renderValues(RenderContext $context, mixed $name, array $variables): string
    {
        $register = $context->getRegister('cycle') ?? [];
        assert(is_array($register));
        $key = $context->evaluate($name);
        $key = match (true) {
            $key instanceof UndefinedVariable => throw $key->toException(),
            is_string($key), is_int($key) => $key,
            $key === null => '',
            is_float($key), is_bool($key) => (string) $key,
            default => throw new InvalidArgumentException('Invalid cycle name'),
        };

        $iteration = match (true) {
            isset($register[$key]) && is_int($register[$key]) => $register[$key],
            default => 0,
        };

        $output = Variable::renderValue($context, $variables[$iteration]);

        $iteration += 1;
        $iteration = $iteration >= count($variables) ? 0 : $iteration;

        $register[$key] = $iteration;
        $context->setRegister('cycle', $register);

        return $output;
    }

    public function parseTreeVisitorChildren(): array
    {
        return $this->name instanceof VariableLookup ? [$this->name, ...$this->variables] : $this->variables;
    }
}
